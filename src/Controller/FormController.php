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


class FormController extends AbstractController
{


    
    #[Route('/api/forms', name: 'app_create_form', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function createCustomForm(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $form = new Form();
    $form->setTitle($data['title']);
    $form->setDescription($data['description'] ?? null);
    $form->setAuthor($this->getUser());
    $form->setIsScorable($data['isScorable'] ?? false);


    foreach ($data['questions'] as $qData) {
        $question = new Question();
        $question->setForm($form);
        $question->setText($qData['text']);
        $question->setType($qData['type']);
        $question->setIsScorable($qData['isScorable'] ?? false);
        $question->setMaxScore($qData['maxScore'] ?? null);

        
        if (isset($qData['options'])) {
            $question->setOptions($qData['options']);
        }
        if (isset($qData['maxScale']) && $qData['type'] === "scale") {
            $question->setMaxScale($qData['maxScale']);
        }

        $form->addQuestion($question);
    }

    $entityManager->persist($form);
    $entityManager->flush();

    return $this->json(['message' => 'Форма создана!', 'id' => $form->getId()]);
}

    #[Route('/api/forms', name: 'list_forms', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function listForms(EntityManagerInterface $entityManager): JsonResponse
    {
        $forms = $entityManager->getRepository(Form::class)->findAll();
        
        $data = array_map(fn($form) => [
            'id' => $form->getId(),
            'title' => $form->getTitle(),
            'description' => $form->getDescription(),
            'questions' => count($form->getQuestions()),
            'authorId' => $form->getAuthor()->getId(), // **Добавляем ID автора**
        ], $forms);

        return $this->json($data);
    }

    #[Route('/api/forms/{id}', name: 'api_view_form', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function apiViewForm(Form $form): JsonResponse
    {
        return $this->json([
            'id' => $form->getId(),
            'title' => $form->getTitle(),
            'description' => $form->getDescription(),
            'questions' => $form->getQuestions()->map(fn($q) => [
                'id' => $q->getId(),
                'text' => $q->getText(),
                'type' => $q->getType(),
                'options' => $q->getOptions()
            ])->toArray()
        ]);
    }
    

    #[Route('/form/{id}', name: 'app_view_form', methods: ['GET'])]
        #[IsGranted('ROLE_USER')]
        public function viewFormPage(Form $form): HttpResponse
        {
            return $this->render('base.html.twig');
        }


        #[Route('/api/forms/{id}/edit', name: 'app_edit_form', methods: ['PUT'])]
        #[IsGranted('ROLE_USER')]
        public function editForm(Request $request, Form $form, EntityManagerInterface $entityManager): JsonResponse
        {
            if ($form->getAuthor() !== $this->getUser()) {
                return $this->json(['error' => 'Вы не можете редактировать эту форму'], 403);
            }
        
            $data = json_decode($request->getContent(), true);
            
            $form->setTitle($data['title'] ?? $form->getTitle());
            $form->setDescription($data['description'] ?? $form->getDescription());
        
            // Обновление списка вопросов
            foreach ($form->getQuestions() as $existingQuestion) {
                $entityManager->remove($existingQuestion);
            }
            $entityManager->flush(); // Удаляем старые вопросы перед добавлением новых
        
            if (isset($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $qData) {
                    $question = new Question();
                    $question->setForm($form);
                    $question->setText($qData['text']);
                    $question->setType($qData['type']);
                    if (isset($qData['options'])) {
                        $question->setOptions($qData['options']);
                    }
                    $form->addQuestion($question);
                    $entityManager->persist($question);
                }
            }
        
            $entityManager->flush();
        
            return $this->json(['message' => 'Форма обновлена!']);
        }
        

    #[Route('/forms/{id}/edit', name: 'app_edit_form_page', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_USER')]
public function editFormPage(Form $form): HttpResponse
{
        return $this->render('base.html.twig');
}


#[Route('/api/forms/{id}/delete', name: 'app_delete_form', methods: ['DELETE'])]
#[IsGranted('ROLE_USER')]
public function deleteForm(Form $form, EntityManagerInterface $entityManager): JsonResponse
{
    if ($form->getAuthor() !== $this->getUser() && !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
        return $this->json(['error' => 'Вы не можете удалить эту форму'], 403);
    }

    // Получаем все ответы, связанные с этой формой
    $responseRepository = $entityManager->getRepository(Response::class);
    $responses = $responseRepository->findBy(['form' => $form]);

    // Удаляем все связанные ответы
    foreach ($responses as $response) {
        $entityManager->remove($response);
    }

    $entityManager->flush(); // Сначала удаляем все ответы

    // Теперь можно удалить саму форму
    $entityManager->remove($form);
    $entityManager->flush(); // Удаляем форму

    return $this->json(['message' => 'Форма и все связанные ответы успешно удалены!']);
}


    #[Route('/api/forms/{id}/submit', name: 'submit_form', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function submitForm(Request $request, Form $form, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $response = new Response();
        $response->setForm($form);
        $response->setUser($this->getUser());
        $response->setAnswers($data['answers']);

        $entityManager->persist($response);
        $entityManager->flush();

        return $this->json(['message' => 'Ответ записан!']);
    }

    #[Route('/forms', name: 'app_form_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function formList(EntityManagerInterface $entityManager): HttpResponse
    {
        $forms = $entityManager->getRepository(Form::class)->findAll();
        
        return $this->render('form/list.html.twig', [
            'forms' => $forms
        ]);
    }

    #[Route('/forms/create', name: 'create_form', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function createFormPage(): HttpResponse
    {
        return $this->render('base.html.twig');
    }

    #[Route('/api/forms/{id}/is-scorable', name: 'check_is_scorable', methods: ['GET'])]
#[IsGranted('ROLE_USER')]
public function checkIsScorable(Form $form): JsonResponse
{
    return $this->json(['isScorable' => $form->getIsScorable()]);
}


}
