<?php

namespace App\Controller;

use App\Entity\Form;
use App\Entity\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FormController extends AbstractController
{
    #[Route('/api/forms', name: 'create_form', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createCustomForm(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $form = new Form();
        $form->setTitle($data['title']);
        $form->setDescription($data['description'] ?? null);
        $form->setQuestions($data['questions']);
        $form->setAuthor($this->getUser());

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
        ], $forms);

        return $this->json($data);
    }

    #[Route('/api/forms/{id}', name: 'view_form', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function viewForm(Form $form): JsonResponse
    {
        return $this->json([
            'id' => $form->getId(),
            'title' => $form->getTitle(),
            'description' => $form->getDescription(),
            'questions' => $form->getQuestions(),
            'author' => $form->getAuthor()->getEmail()
        ]);
    }

    #[Route('/api/forms/{id}/edit', name: 'edit_form', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function editForm(Request $request, Form $form, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($form->getAuthor() !== $this->getUser()) {
            return $this->json(['error' => 'Вы не можете редактировать эту форму'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $form->setTitle($data['title'] ?? $form->getTitle());
        $form->setDescription($data['description'] ?? $form->getDescription());
        $form->setQuestions($data['questions'] ?? $form->getQuestions());

        $entityManager->flush();

        return $this->json(['message' => 'Форма обновлена!']);
    }

    #[Route('/api/forms/{id}/delete', name: 'delete_form', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteForm(Form $form, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($form->getAuthor() !== $this->getUser()) {
            return $this->json(['error' => 'Вы не можете удалить эту форму'], 403);
        }

        $entityManager->remove($form);
        $entityManager->flush();

        return $this->json(['message' => 'Форма удалена!']);
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
}
