<?php

namespace Glsv\DiadocApi\responses;

use Glsv\DiadocApi\interfaces\ApiResponseInterface;

class ErrorResponse implements ApiResponseInterface
{
    public function __construct(public int $httpdCode, public string $message)
    {
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