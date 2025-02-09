<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailVerificationService
{

    public function __construct(
        private HttpClientInterface $client,
        private ParameterBagInterface $params
        )
    {
        $this->client = $client;
        $this->params = $params;
    }

    public function isValidEmail(string $email): bool
    {
        $apiKey = $this->params->get('mailboxlayer.api_key');

        $response = $this->client->request(
            'GET',
            'https://apilayer.net/api/check',
            [
                'query' => [
                    'access_key' => $apiKey,
                    'email' => $email,
                    'smtp' => 1,
                    'format' => 1
                ]
            ]
        );

        $result = $response->toArray();

        return $result['format_valid'] && $result['mx_found'] && $result['smtp_check']  && !$result['disposable'];
    }
}
