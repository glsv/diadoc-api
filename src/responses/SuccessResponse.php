<?php

namespace Glsv\DiadocApi\responses;

use Glsv\DiadocApi\interfaces\ApiResponseInterface;
use Glsv\DiadocApi\traits\{ResponseNoRetryTrait, ResponseSuccessTrait};

class SuccessResponse implements ApiResponseInterface
{
    use ResponseNoRetryTrait;
    use ResponseSuccessTrait;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}