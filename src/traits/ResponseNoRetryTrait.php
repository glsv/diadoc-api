<?php

namespace Glsv\DiadocApi\traits;

trait ResponseNoRetryTrait
{
    public function isRetryRequired(): bool
    {
        return false;
    }

    public function getRetryTime(): int
    {
        return 0;
    }
}