<?php

namespace Glsv\DiadocApi\responses;

use Glsv\DiadocApi\interfaces\ApiResponseInterface;

class SuccessResponse implements ApiResponseInterface
{
    public function __construct(protected array $data)
    {
    }

    public function isError(): bool
    {
        return false;
    }

    public function getError(): string
    {
        return  '';
    }

    public function getData(): array
    {
        return $this->data;
    }
}