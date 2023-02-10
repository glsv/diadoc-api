<?php

declare(strict_types=1);

namespace Glsv\DiadocApi\responses;

use Glsv\DiadocApi\interfaces\ApiResponseInterface;
use Glsv\DiadocApi\traits\ResponseSuccessTrait;

class RetryResponse implements ApiResponseInterface
{
    use ResponseSuccessTrait;

    /**
     * @var int время (в секундах), по прошествии которого имеет смысл повторить запрос.
     */
    private int $retryAfter;

    public function __construct(int $retryAfter)
    {
        $this->retryAfter = $retryAfter;
    }

    public function getData(): array
    {
        return [];
    }

    public function isRetryRequired(): bool
    {
        return true;
    }

    public function getRetryTime(): int
    {
        return $this->retryAfter;
    }
}