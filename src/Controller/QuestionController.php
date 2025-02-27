<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Form;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class QuestionController extends AbstractController
{
    #[Route('/api/forms/{id}/questions', name: 'add_question', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function addQuestion(Request $request, Form $form, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $question = new Question();
    $question->setForm($form);
    $question->setText($data['text']);
    $question->setType($data['type']);

    if (isset($data['options'])) {
        $question->setOptions($data['options']);
    }
    if (isset($data['maxScale']) && $data['type'] === "scale") {
        $question->setMaxScale($data['maxScale']);
    }

    // ✅ Добавляем поддержку оценки вопросов
    if (isset($data['isScorable'])) {
        $question->setIsScorable($data['isScorable']);
    }
    if (isset($data['maxScore'])) {
        $question->setMaxScore($data['maxScore']);
    }

    $entityManager->persist($question);
    $entityManager->flush();

    return $this->json(['message' => 'Вопрос добавлен!', 'id' => $question->getId()]);
}

#[Route('/api/questions/{id}', name: 'edit_question', methods: ['PUT'])]
#[IsGranted('ROLE_USER')]
public function editQuestion(Request $request, Question $question, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (isset($data['text'])) {
        $question->setText($data['text']);
    }
    if (isset($data['type'])) {
        $question->setType($data['type']);
    }
    if (isset($data['options'])) {
        $question->setOptions($data['options']);
    }
    if (isset($data['maxScale']) && $data['type'] === "scale") {
        $question->setMaxScale($data['maxScale']);
    }

    // ✅ Добавляем поддержку редактирования `isScorable` и `maxScore`
    if (isset($data['isScorable'])) {
        $question->setIsScorable($data['isScorable']);
    }
    if (isset($data['maxScore'])) {
        $question->setMaxScore($data['maxScore']);
    }

    $entityManager->flush();

    return $this->json(['message' => 'Вопрос обновлен!']);
}

    #[Route('/api/questions/{id}', name: 'delete_question', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteQuestion(Question $question, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($question);
        $entityManager->flush();

        return $this->json(['message' => 'Вопрос удален!']);
    }

    #[Route('/api/forms/{id}/questions', name: 'get_questions', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getQuestions(Form $form): JsonResponse
    {
        $questions = $form->getQuestions();

        $data = array_map(fn($question) => [
            'id' => $question->getId(),
            'text' => $question->getText(),
            'type' => $question->getType(),
            'options' => $question->getOptions(),
            'maxScale' => $question->getMaxScale(),
        ], $questions->toArray());

        return $this->json($data);
    }
}
