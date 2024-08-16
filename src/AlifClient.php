<?php

namespace RasulSafarovitch\AlifPaymentIntegration;

use GuzzleHttp\Client;
use RasulSafarovitch\AlifPaymentIntegration\Interfaces\AlifClientInterface;
use RasulSafarovitch\AlifPaymentIntegration\Exceptions\AlifException;

class AlifClient implements AlifClientInterface
{
    protected $httpClient;
    protected $key;
    protected $password;
    protected $hmac_password;

    public function __construct(string $key, string $password)
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://invoices.alif.tj/',
            'headers' => ['Accept' => 'application/json']
        ]);
        $this->key = $key;
        $this->hmac_password = hash_hmac('sha256', $password, $key);
    }

    protected function generateToken(string $stringToHash): string
    {
        return hash_hmac('sha256', $stringToHash, $this->password);
    }

    public function createInvoice(array $data): array
    {
        $tokenString = $this->key . $data['orderid'] . number_format($data['price'], 2) . $data['phone'];
        $token = $this->generateToken($tokenString);

        $response = $this->sendRequest('create', $data, $token);
        return $response;
    }

    public function checkStatus(int $invoiceId): array
    {
        $tokenString = $this->key . $invoiceId;
        $token = $this->generateToken($tokenString);

        $response = $this->sendRequest('status', ['invoiceid' => $invoiceId], $token);
        return $response;
    }

    public function cancelInvoice(int $invoiceId): array
    {
        $tokenString = $this->key . $invoiceId;
        $token = $this->generateToken($tokenString);

        $response = $this->sendRequest('cancel', ['invoiceid' => $invoiceId], $token);
        return $response;
    }

    protected function sendRequest(string $endpoint, array $data, string $token): array
    {
        try {
            $response = $this->httpClient->post($endpoint, [
                'headers' => ['Token' => $token],
                'json' => array_merge(['key' => $this->key], $data)
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            return $body;
        } catch (\Exception $e) {
            throw new AlifException("Request failed: " . $e->getMessage());
        }
    }
}
