<?php

declare(strict_types=1);

namespace Glsv\DiadocApi;

use Glsv\DiadocApi\exceptions\{DiadocApiUnauthorizedException, DiadocRuntimeApiException, DiadocInvalidParamsException};
use Glsv\DiadocApi\interfaces\{ApiResponseInterface, AuthenticatorInterface, TokenStorageInterface};
use Glsv\DiadocApi\responses\{ErrorResponse, SuccessResponse};
use Glsv\DiadocApi\services\TokenStorage;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class DiadocClientApi
{
    const DIADOC_SCHEMA = 'DiadocAuth';

    protected string $baseUrl;
    protected string $developer_key;
    protected AuthenticatorInterface $authenticator;
    protected TokenStorageInterface $storage;

    /**
     * @var Client|ClientInterface
     */
    protected $client;

    protected string $domain = '';

    protected array $defaultHeaders = [
        'Content-Type' => 'application/json; utf-8',
    ];

    public function __construct(
        string $baseUrl,
        string $developer_key,
        AuthenticatorInterface $authenticator,
        TokenStorageInterface $storage = null,
        ?ClientInterface $httpClient = null)
    {
        $domain = parse_url($baseUrl, PHP_URL_HOST);
        if (!$domain) {
            throw new DiadocInvalidParamsException('couldn`t receive domain from baseUrl: ' . $baseUrl);
        }

        $this->baseUrl = $baseUrl;
        $this->domain = $domain;
        $this->developer_key = $developer_key;
        $this->authenticator = $authenticator;

        if ($storage === null) {
            $this->storage = new TokenStorage();
        }

        if ($httpClient) {
            $this->client = $httpClient;

        } else {
            $this->client = new Client(['base_uri' => $baseUrl]);
        }
    }


    public function authByPassword(string $login, string $password): string
    {
        $requestBody = [
            'login' => $login,
            'password' => $password,
        ];

        try {
            $response = $this->client->post('V3/Authenticate?type=password', [
                'headers' => array_merge($this->defaultHeaders, [
                    'Authorization' => sprintf(
                        '%s ddauth_api_client_id=%s', self::DIADOC_SCHEMA, $this->developer_key
                    )
                ]),
                'http_errors' => false,
                'body' => json_encode($requestBody),
            ]);

            return $response->getBody()->getContents();
        } catch (\Throwable $exc) {
            throw new DiadocRuntimeApiException(
                "Error execute request. \n" .
                "Origin error: " . $exc->getMessage() . "\n",
                $exc->getCode(),
                $exc
            );
        }
    }

    public function makeGet(string $url, array $params): ApiResponseInterface
    {
        try {
            $response = $this->client->get($url, [
                'headers' => $this->prepareWorkHeaders(),
                'http_errors' => false,
                'query' => $params,
            ]);

            return $this->handleResponse($response);
        } catch (\Throwable $exc) {
            throw new DiadocRuntimeApiException(
                "Error execute request. \n" .
                "Origin error: " . $exc->getMessage() . "\n",
                $exc->getCode(),
                $exc
            );
        }
    }

    protected function handleResponse(ResponseInterface $response): ApiResponseInterface
    {
        $statusCode = $response->getStatusCode();

        switch ($statusCode) {
            case 401:
                throw new DiadocApiUnauthorizedException("401 Unauthorized.", $statusCode);
            case 403:
                throw new DiadocApiUnauthorizedException("403 Forbidden.", $statusCode);
            case 400:
                return new ErrorResponse($statusCode, (string)$response->getBody());
        }

        try {
            $data = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exc) {
            throw new DiadocRuntimeApiException(
                "Https status code: $statusCode \n" .
                "Error parsing response:\n------------" .
                mb_substr((string)$response->getBody(), 0, 100) . "...\n" .
                "------------\n"
            );
        }

        return new SuccessResponse($data);
    }

    protected function prepareWorkHeaders(): array
    {
        return array_merge($this->defaultHeaders, [
            'Authorization' => $this->BuildAuthString(),
        ]);
    }

    protected function  BuildAuthString(): string
    {
        $token = $this->storage->get();

        if ($token === null) {
            $this->storage->save($this->authenticator->getToken($this));
            $token = $this->storage->get();
        }

        return sprintf(
            '%s ddauth_api_client_id=%s, ddauth_token=%s',
            self::DIADOC_SCHEMA,
            $this->developer_key,
            $token
        );
    }
}