<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailVerificationService
{

    public function __construct(
        private HttpClientInterface $client,
        private ParameterBagInterface $params,
        private LoggerInterface $logger
        )
    {
        $this->client = $client;
        $this->params = $params;
        $this->logger = $logger;
    }

    public function isValidEmail(string $email): bool
    {
        $apiKey = $this->params->get('mailboxlayer.api_key');
    
        try {
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
    
            if (!isset($result['format_valid'], $result['mx_found'], $result['smtp_check'], $result['disposable'])) {
                throw new \RuntimeException("Invalid API response structure.");
            }
    
            return $result['format_valid'] 
                && $result['mx_found'] 
                && $result['smtp_check'] 
                && !$result['disposable'];
                
        } catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
            $this->logger->error("Network error while validating email: " . $e->getMessage());
            return false;
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $this->logger->error("Client error while validating email: " . $e->getMessage());
            return false;
        } catch (\Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface $e) {
            $this->logger->error("Server error while validating email: " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->logger->error("Unexpected error while validating email: " . $e->getMessage());
            return false;
        }
    }
    
}
