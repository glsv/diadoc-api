<?php

namespace Glsv\DiadocApi\interfaces;

interface ApiResponseInterface
{
    public function isError(): bool;

    public function getError(): string;

    public function getData(): array;
}