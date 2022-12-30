<?php

declare(strict_types=1);

namespace Glsv\DiadocApi\services;

use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\interfaces\{ApiResponseInterface, RequestInterface};
use Glsv\DiadocApi\exceptions\DiadocRuntimeApiException;
use Glsv\DiadocApi\vo\RequestMethod;

class CommandExecutor
{
    public function __construct(DiadocClientApi $api)
    {
        $this->api = $api;
    }

    public function executeRequest(RequestMethod $method, string $url, RequestInterface $request): ApiResponseInterface
    {
        if ($method->getValue() == RequestMethod::METHOD_GET) {
            return $this->api->makeGet($url, $request->buildBody());
        }

        throw new DiadocRuntimeApiException('not implemented');
    }
}