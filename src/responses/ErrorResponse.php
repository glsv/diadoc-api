<?php

namespace Glsv\DiadocApi\responses;

use Glsv\DiadocApi\interfaces\ApiResponseInterface;
use Glsv\DiadocApi\traits\ResponseNoRetryTrait;

class ErrorResponse implements ApiResponseInterface
{
    use ResponseNoRetryTrait;

    public int $httpdCode;
    public string $message;

    public function __construct(int $httpdCode, string $message)
    {
        $this->httpdCode = $httpdCode;
        $this->message = $message;
    }

    public function isError(): bool
    {
        return true;
    }

    public function getError(): string
    {
        return $this->message;
    }

    public function getData(): array
    {
        return  [];
    }
}