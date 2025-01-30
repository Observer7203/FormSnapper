<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class UserController extends AbstractController
{
    #[Route('/api/users', name: 'api_users_list', methods: ['GET'])]
    public function list(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $data = array_map(fn(User $user) => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'status' => $user->getStatus()->value,
            'lastLogin' => $user->getLastLogin()?->format('Y-m-d H:i:s'),
        ], $users);

        return $this->json($data);
    }

    #[Route('/api/users/{id}/block', name: 'api_users_block', methods: ['PATCH'])]
    public function block(
        int $id,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        RouterInterface $router
    ): Response {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        // Меняем статус на заблокированный
        $user->setStatus(UserStatus::Blocked);
        $em->flush();

        // Если пользователь блокирует сам себя, разлогиниваем и редиректим на логин
        if ($user === $this->getUser()) {
            $tokenStorage->setToken(null);
            $session->invalidate();

            return $this->json([
                'status' => 'User blocked',
                'logout' => true, // Флаг для фронта, чтобы он редиректил
                'redirect' => $router->generate('app_login'),
            ]);
        }

        // Если все пользователи заблокированы, разлогиниваем всех
        $activeUsers = $userRepository->count(['status' => UserStatus::Active]);
        if ($activeUsers === 0) {
            $tokenStorage->setToken(null);
            $session->invalidate();

            return $this->json([
                'status' => 'All users blocked',
                'logout' => true,
                'redirect' => $router->generate('app_login'),
            ]);
        }

        return $this->json(['status' => 'User blocked']);
    }

    #[Route('/api/users/{id}/unblock', name: 'api_users_unblock', methods: ['PATCH'])]
    public function unblock(int $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $user->setStatus(UserStatus::Active);
        $em->flush();

        return $this->json(['status' => 'User unblocked']);
    }

    #[Route('/api/users/{id}', name: 'api_users_delete', methods: ['DELETE'])]
    public function delete(int $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $em->remove($user);
        $em->flush();

        return $this->json(['status' => 'User deleted']);
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('base.html.twig');
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/users', name: 'app_users', methods: ['GET'])]
    public function users(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
