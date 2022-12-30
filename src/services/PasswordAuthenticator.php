<?php

namespace Glsv\DiadocApi\services;

use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\interfaces\AuthenticatorInterface;

class PasswordAuthenticator implements AuthenticatorInterface
{
    public function __construct(protected string $login, protected string $password)
    {
    }

    public function getToken(DiadocClientApi $api): string
    {
        return $api->authByPassword($this->login, $this->password);
    }
}