<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SalesforceOAuthService;

class SalesforceController extends AbstractController
{
    private $salesforceService;

    public function __construct(SalesforceOAuthService $salesforceService)
    {
        $this->salesforceService = $salesforceService;
    }

    #[Route('/oauth/login', name: 'salesforce_login')]
    public function login()
    {
        // Отладка: проверяем, что URL OAuth формируется корректно
        $authUrl = $this->salesforceService->getAuthorizationUrl();
        if (!$authUrl) {
            return new Response('Ошибка: Authorization URL не сгенерирован', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('Redirecting to Salesforce...', Response::HTTP_FOUND, [
            'Location' => $authUrl,
        ]);
    }

    #[Route('/oauth/callback', name: 'salesforce_callback')]
    public function callback(Request $request)
    {
        $code = $request->query->get('code');
        if (!$code) {
            return new Response('Ошибка: Код авторизации отсутствует', Response::HTTP_BAD_REQUEST);
        }

        try {
            $token = $this->salesforceService->getAccessToken($code);
            return new Response('Access Token: ' . $token->getToken());
        } catch (\Exception $e) {
            return new Response('Ошибка при получении токена: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
