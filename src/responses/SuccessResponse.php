<?php

namespace Glsv\DiadocApi\responses;

use Glsv\DiadocApi\interfaces\ApiResponseInterface;

class SuccessResponse implements ApiResponseInterface
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
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