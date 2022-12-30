<?php

declare(strict_types=1);

namespace Glsv\DiadocApi\interfaces;

interface TokenStorageInterface
{
    public function get(): ?string;

    public function save(string $token): void;
}