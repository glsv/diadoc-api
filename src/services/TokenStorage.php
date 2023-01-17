<?php

declare(strict_types=1);

namespace Glsv\DiadocApi\services;

use Glsv\DiadocApi\exceptions\DiadocInvalidParamsException;
use Glsv\DiadocApi\exceptions\DiadocRuntimeApiException;
use Glsv\DiadocApi\interfaces\TokenStorageInterface;

class TokenStorage implements TokenStorageInterface
{
    private string $dir;
    private static ?string $token = null;

    public function __construct(?string $dir = null)
    {
        if (!$dir) {
            $this->dir = sys_get_temp_dir();
        } else {
            if (!file_exists($dir)) {
                throw new DiadocInvalidParamsException('Dir is no not exist: ' . $dir);
            }

            if (!is_writable($dir)) {
                throw new DiadocInvalidParamsException('Dir is no not writable: ' . $dir);
            }

            $this->dir = $dir;
        }
    }

    public function save(string $token): void
    {
        if ($token === "") {
            throw new DiadocInvalidParamsException("token is empty");
        }

        if(!file_put_contents($this->getFilename(), $token)) {
            throw new DiadocRuntimeApiException('Error save token to file: ' . $this->getFilename());
        }

        self::$token = $token;
    }

    public function get(): ?string
    {
        if (self::$token == null) {
            $filename = $this->getFilename();

            if (!file_exists($filename)) {
                return null;
            }

            self::$token = file_get_contents($filename);
        }

        return self::$token;
    }

    private function getFilename(): string
    {
        return $this->dir . DIRECTORY_SEPARATOR . 'diadoc_token.txt';
    }

    public function clear(): void
    {
        self::$token = null;

        if (file_exists($this->getFilename())) {
            unlink($this->getFilename());
        }
    }
}