<?php

declare(strict_types=1);

namespace Glsv\DiadocApi\services;

use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\interfaces\{ApiResponseInterface, RequestInterface};
use Glsv\DiadocApi\vo\RequestMethod;

class CommandExecutor
{
    public function __construct(protected DiadocClientApi $api)
    {
    }

    public function executeRequest(RequestMethod $method, string $url, RequestInterface $request): ApiResponseInterface
    {
        return match ($method) {
            RequestMethod::GET => $this->api->makeGet($url, $request->buildBody()),
            RequestMethod::POST => $this->api->makeGet($url, $request->buildBody()),
        };
    }
}