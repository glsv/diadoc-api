<?php

declare(strict_types=1);

namespace Glsv\DiadocApi;

use Glsv\DiadocApi\exceptions\{DiadocApiFailAuthException,
    DiadocApiUnauthorizedException,
    DiadocRuntimeApiException,
    DiadocInvalidParamsException};
use Glsv\DiadocApi\interfaces\{ApiResponseInterface, AuthenticatorInterface, TokenStorageInterface};
use Glsv\DiadocApi\responses\{ErrorResponse, SuccessFileResponse, SuccessResponse};
use Glsv\DiadocApi\dto\FileDto;
use Glsv\DiadocApi\helpers\FilenameResponseGetter;
use Glsv\DiadocApi\services\TokenStorage;
use Glsv\DiadocApi\vo\RequestMethod;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class DiadocClientApi
{
    const DIADOC_SCHEMA = 'DiadocAuth';

    protected string $baseUrl;
    protected string $developer_key;
    protected AuthenticatorInterface $authenticator;
    protected TokenStorageInterface $tokenStorage;

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
            $this->tokenStorage = new TokenStorage();
        } else {
            $this->tokenStorage = $storage;
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

        } catch (\Throwable $exc) {
            throw new DiadocRuntimeApiException(
                "Error execute request. \n" .
                "Origin error: " . $exc->getMessage() . "\n",
                $exc->getCode(),
                $exc
            );
        }

        $responseObj = $this->handleResponse($response);

        if ($responseObj->isError()) {
            throw new DiadocApiFailAuthException(
                "Error auth by passwd request. \n" .
                "Origin error: " . $responseObj->getError() . "\n",
                401
            );
        }

        return $responseObj->getData()[0];
    }

    public function executeGet(string $url, array $params): ApiResponseInterface
    {
        return $this->performRequest($url, $params, new RequestMethod(RequestMethod::METHOD_GET));
    }

    protected function performRequest(string $url, array $params, RequestMethod $method, bool $repeated = false): ApiResponseInterface
    {
        try {
            if ($method->getValue() == RequestMethod::METHOD_GET) {
                $response = $this->performGetRequest($url, $params);
            } else {
                throw new DiadocRuntimeApiException('is not implemented');
            }

            return $this->handleResponse($response);
        } catch (DiadocApiUnauthorizedException $exc) {
            if (!$repeated) {
                $this->tokenStorage->clear();
                return $this->performRequest($url, $params, $method, true);
            }

            throw $exc;
        } catch (DiadocApiFailAuthException $exc) {
            throw $exc;
        } catch (\Throwable $exc) {
            throw new DiadocRuntimeApiException(
                "Error execute request. \n" .
                "Origin error: " . $exc->getMessage() . "\n",
                $exc->getCode(),
                $exc
            );
        }
    }

    protected function performGetRequest(string $url, array $params): ResponseInterface
    {
        return $this->client->get($url, [
            'headers' => $this->prepareWorkHeaders(),
            'http_errors' => false,
            'query' => $params,
        ]);
    }

    protected function handleResponse(ResponseInterface $response): ApiResponseInterface
    {
        $statusCode = $response->getStatusCode();
        $responseBody = (string)$response->getBody();

        switch ($statusCode) {
            case 400:
                return new ErrorResponse($statusCode, $responseBody);
            case 401:
                throw new DiadocApiUnauthorizedException(
                    $this->msgErr("401 Unauthorized.", $responseBody), $statusCode
                );
            case 402:
                throw new DiadocApiUnauthorizedException(
                    $this->msgErr("402 Forbidden. Payment Required. ", $responseBody), $statusCode
                );
            case 403:
                throw new DiadocApiUnauthorizedException(
                    $this->msgErr("403 Forbidden.", $responseBody), $statusCode
                );
            case 404:
                throw new DiadocRuntimeApiException(
                    $this->msgErr("404 Not Found.", $responseBody), $statusCode
                );
            case 405:
                throw new DiadocRuntimeApiException(
                    $this->msgErr("Method Not Allowed.", $responseBody), $statusCode
                );
            case 500:
                throw new DiadocRuntimeApiException(
                    $this->msgErr("500 Internal Server Error .", $responseBody), $statusCode
                );
        }

        if ($statusCode !== 200) {
            throw new DiadocRuntimeApiException('Can`t handle response with status code = ' . $statusCode);
        }

        return $this->parseResponse($response, $responseBody);
    }

    private function parseResponse(ResponseInterface $response, string $responseBody): ApiResponseInterface
    {
        $contentTypes = $response->getHeader('Content-Type');

        if (empty($contentTypes) || strpos($contentTypes[0], 'text/plain') === 0) {
            return new SuccessResponse([$responseBody]);
        }

        $contentType = $contentTypes[0];

        if ($contentType === "application/pdf") {
            return new SuccessFileResponse(
                new FileDto('xxx', $contentType, FilenameResponseGetter::getFilename($response))
            );
        }

        if (strpos($contentType, 'application/json') !== 0) {
            throw new DiadocRuntimeApiException('Unknown contentType: ' . $contentType);
        }

        try {
            $data = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exc) {
            throw new DiadocRuntimeApiException(
                "Https status code: " . $response->getStatusCode() . " \n" .
                "Error parsing response:\n------------" .
                mb_substr((string)$response->getBody(), 0, 100) . "...\n" .
                "------------\n"
            );
        }

        return new SuccessResponse($data);
    }

    private function msgErr(string $prefix, $response): string
    {
        return $prefix .' Response: ' . $response;
    }

    protected function prepareWorkHeaders(): array
    {
        return array_merge($this->defaultHeaders, [
            'Authorization' => $this->BuildAuthString(),
        ]);
    }

    protected function  BuildAuthString(): string
    {
        return sprintf(
            '%s ddauth_api_client_id=%s, ddauth_token=%s',
            self::DIADOC_SCHEMA,
            $this->developer_key,
            $this->getTokenForApi()
        );
    }

    protected function getTokenForApi()
    {
        $token = $this->tokenStorage->get();
        if ($token === null) {
            $this->tokenStorage->save($this->authenticator->getToken($this));
        }

        return  $this->tokenStorage->get();
    }
}