<?php

namespace Glsv\DiadocApi\responses;

use Glsv\DiadocApi\dto\FileDto;
use Glsv\DiadocApi\interfaces\ApiResponseInterface;

class SuccessFileResponse implements ApiResponseInterface
{
    protected FileDto $fileDto;
    public function __construct(FileDto $fileDto)
    {
        $this->fileDto = $fileDto;
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
        return [$this->fileDto];
    }
}