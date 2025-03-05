<?php 

namespace App\Service;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Log\LoggerInterface;

class SalesforceOAuthService
{
    private $provider;
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->provider = new GenericProvider([
            'clientId'                => $_ENV['SALESFORCE_CLIENT_ID'],
            'clientSecret'            => $_ENV['SALESFORCE_CLIENT_SECRET'],
            'redirectUri'             => $_ENV['SALESFORCE_REDIRECT_URI'],
            'urlAuthorize'            => $_ENV['SALESFORCE_AUTH_URL'],
            'urlAccessToken'          => $_ENV['SALESFORCE_TOKEN_URL'],
            'urlResourceOwnerDetails' => '',
        ]);

        $this->logger = $logger;
    }

    public function getAuthorizationUrl(): string
    {
        return $this->provider->getAuthorizationUrl();
    }

    public function getAccessToken(string $code): ?AccessToken
    {
        try {
            return $this->provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Ошибка при получении access_token: ' . $e->getMessage());
            return null;
        }
    }
}
