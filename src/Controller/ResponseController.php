<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\Response;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ResponseController extends AbstractController
{
    #[Route('/api/forms/{id}/responses', name: 'list_form_responses', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function listFormResponses(Form $form, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if ($user !== $form->getAuthor() && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Доступ запрещен.'], 403);
        }

        $responses = $entityManager->getRepository(Response::class)->findBy(['form' => $form]);

        $data = array_map(fn($response) => [
            'id' => $response->getId(),
            'user' => $response->getUser()->getEmail(),
            'reviewed' => $response->getScore() !== null,
            'score' => $response->getScore(),
        ], $responses);

        return $this->json($data);
    }

    #[Route('/api/forms/{id}/responses/{responseId}', name: 'view_user_response', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function viewUserResponse(Form $form, int $responseId, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = $entityManager->getRepository(Response::class)->find($responseId);
    
        if (!$response || $response->getForm()->getId() !== $form->getId()) {
            return $this->json(['error' => 'Ответ не найден или не относится к данной форме.'], 404);
        }
    
        $user = $this->getUser();
        if ($user !== $form->getAuthor() && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Доступ запрещен.'], 403);
        }
    
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
    
            if (isset($data['scores']) && is_array($data['scores'])) {
                $scores = [];
                $totalScore = 0;
                $maxScore = 0;
    
                foreach ($form->getQuestions() as $question) {
                    $questionId = $question->getId();
                    if (isset($data['scores'][$questionId])) {
                        $scoreValue = (int) $data['scores'][$questionId];
                        $scores[$questionId] = min($scoreValue, $question->getMaxScore() ?? 1);
                        $totalScore += $scores[$questionId];
                        $maxScore += $question->getMaxScore() ?? 1;
                    }
                }
    
                $response->setScores($scores);
                $response->setScore($totalScore . " / " . $maxScore);
                $entityManager->flush();
    
                return $this->json(['message' => 'Оценки успешно сохранены.']);
            }
        }
    
        $questionsData = [];
        foreach ($form->getQuestions() as $question) {
            $questionsData[] = [
                'id' => $question->getId(),
                'text' => $question->getText(),
                'type' => $question->getType(),
                'maxScore' => $question->getMaxScore(),
            ];
        }
    
        return $this->json([
            'id' => $response->getId(),
            'user' => $response->getUser()->getEmail(),
            'answers' => $response->getAnswers(),
            'score' => $response->getScore(),
            'scores' => $response->getScores() ?? [], // Добавлено исправление
            'questions' => $questionsData,
        ]);
    }
    
    
    #[Route('/api/forms/{id}/my-response', name: 'view_my_response', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function viewMyResponse(Form $form, EntityManagerInterface $entityManager): JsonResponse
    {
        $response = $entityManager->getRepository(Response::class)->findOneBy([
            'form' => $form,
            'user' => $this->getUser()
        ]);

        if (!$response) {
            return $this->json(['message' => 'Вы еще не заполняли эту форму.'], 404);
        }

        return $this->json([
            'id' => $response->getId(),
            'answers' => $response->getAnswers(),
            'reviewed' => $response->getScore() !== null,
            'score' => $response->getScore(),
        ]);
    }


    #[Route('/form/{id}/my-response', name: 'form_review', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function viewResponsePage(Form $form): HttpResponse
    {
        return $this->render('base.html.twig');
    }

    #[Route('/form/{id}/responses/{responseId}', name: 'form_review_admin', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function adminResponsePage(Form $form): HttpResponse
    {
        return $this->render('base.html.twig');
    }


    #[Route('/api/forms/{id}/responses/{responseId}/score', name: 'save_response_score', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function saveResponseScore(Form $form, int $responseId, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
    
        // Находим response по ID вручную
        $response = $entityManager->getRepository(Response::class)->find($responseId);
        if (!$response) {
            return $this->json(['error' => 'Ответ не найден.'], 404);
        }
    
        if ($user !== $form->getAuthor() && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Доступ запрещен.'], 403);
        }
    
        $data = json_decode($request->getContent(), true);
        if (!isset($data['scores']) || !is_array($data['scores'])) {
            return $this->json(['error' => 'Некорректные данные.'], 400);
        }
    
        // Устанавливаем оценки и сохраняем в БД
        $response->setScores($data['scores']);
    
        // Автоматически считаем итоговую оценку (сумма всех оценок)
        $totalScore = array_sum($data['scores']);
        $response->setScore($totalScore);
    
        $entityManager->flush();
    
        return $this->json([
            'message' => 'Оценки успешно сохранены.',
            'total_score' => $totalScore
        ]);
    }


}
