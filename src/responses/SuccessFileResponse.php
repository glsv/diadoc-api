<?php

namespace Glsv\DiadocApi\responses;

use Glsv\DiadocApi\dto\FileDto;
use Glsv\DiadocApi\interfaces\ApiResponseInterface;
use Glsv\DiadocApi\traits\{ResponseNoRetryTrait, ResponseSuccessTrait};

class SuccessFileResponse implements ApiResponseInterface
{
    use ResponseNoRetryTrait;
    use ResponseSuccessTrait;

    protected FileDto $fileDto;
    public function __construct(FileDto $fileDto)
    {
        $this->fileDto = $fileDto;
    }

    public function getData(): array
    {
        return [$this->fileDto];
    }
}