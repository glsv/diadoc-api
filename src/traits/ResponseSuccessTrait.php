<?php

declare(strict_types=1);

namespace Glsv\DiadocApi\traits;

trait ResponseSuccessTrait
{
    public function isError(): bool
    {
        return false;
    }

    public function getError(): string
    {
        return  '';
    }
}