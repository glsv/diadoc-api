<?php


use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\exceptions\DiadocApiUnauthorizedException;
use Glsv\DiadocApi\exceptions\DiadocRuntimeApiException;
use Glsv\DiadocApi\interfaces\TokenStorageInterface;
use Glsv\DiadocApi\services\PasswordAuthenticator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class DiadocClientApiTest extends TestCase
{
    private $baseUrl = 'http://xyz.com';
    private $developerToken = 'dev_token';
    private $login = 'login';
    private $passwd = 'passwd';

    public function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)->getMock();
        $this->authenticator = new PasswordAuthenticator($this->login, $this->passwd);

        $this->storage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $this->storage->method('get')->willReturn('token');
    }

    public function testSuccessAuth()
    {
        $tokenForCompare = 'token_001';

        $response = new Response(200, ['Content-Type' => 'text/plain; charset=utf-8'], $tokenForCompare);
        $this->client->method('post')->willReturn($response);

        $api = new DiadocClientApi(
            $this->baseUrl,
            $this->developerToken,
            $this->authenticator,
            null,
            $this->client
        );

        $this->assertSame($tokenForCompare, $api->authByPassword($this->login, $this->passwd));
    }

    public function testWrongAuth()
    {
        $this->expectException(DiadocApiUnauthorizedException::class);

        $response = new Response(401, [], 'Wrong password provided for login email@globus-ltd.com.');
        $this->client->method('post')->willReturn($response);

        $api = new DiadocClientApi(
            $this->baseUrl,
            $this->developerToken,
            $this->authenticator,
            null,
            $this->client
        );

        $api->authByPassword($this->login, $this->passwd);
    }

    public function testError400()
    {
        $response = new Response(400, [], 'error message');
        $this->client->method('get')->willReturn($response);

        $api = new DiadocClientApi(
            $this->baseUrl,
            $this->developerToken,
            $this->authenticator,
            $this->storage,
            $this->client
        );

        $response = $api->executeGet('/rel_url', []);

        $this->assertTrue($response->isError());
    }

    /**
     * @dataProvider ErrorHttpCodes
     */
    public function testErrors($exceptionClass, $statusCode, $errMessage)
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($errMessage);

        $response = new Response($statusCode, [], 'error message');
        $this->client->method('get')->willReturn($response);

        $api = new DiadocClientApi(
            $this->baseUrl,
            $this->developerToken,
            $this->authenticator,
            $this->storage,
            $this->client
        );

        $response = $api->executeGet('/rel_url', []);

        $this->assertTrue($response->isError());
    }

    public function testRetryResponse()
    {
        $response = new Response(
            200,
            [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Retry-After' => '20'
            ],
            ''
        );

        $this->client->method('get')->willReturn($response);

        $api = new DiadocClientApi(
            $this->baseUrl,
            $this->developerToken,
            $this->authenticator,
            null,
            $this->client
        );

        $resultResponse = $api->executeGet('/path', []);

        $this->assertTrue($resultResponse->isRetryRequired());
    }

    public function ErrorHttpCodes(): array
    {
        return [
            [DiadocApiUnauthorizedException::class, 401, '401 Unauthorized'],
            [DiadocApiUnauthorizedException::class, 402, '402 Forbidden. Payment Required'],
            [DiadocApiUnauthorizedException::class, 403, '403 Forbidden'],
            [DiadocRuntimeApiException::class, 404, '404 Not Found'],
            [DiadocRuntimeApiException::class,405, 'Method Not Allowed'],
            [DiadocRuntimeApiException::class, 500, '500 Internal Server Error'],
            [DiadocRuntimeApiException::class, 502, 'Can`t handle response with status code ='],
        ];
    }
}
