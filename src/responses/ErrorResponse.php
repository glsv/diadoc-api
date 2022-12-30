<?php

namespace Glsv\DiadocApi\responses;

use Glsv\DiadocApi\interfaces\ApiResponseInterface;

class ErrorResponse implements ApiResponseInterface
{
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