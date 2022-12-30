<?php

namespace Glsv\DiadocApi\services;

use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\interfaces\AuthenticatorInterface;

class PasswordAuthenticator implements AuthenticatorInterface
{
    protected string $login;
    protected string $password;

    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function getToken(DiadocClientApi $api): string
    {
        return $api->authByPassword($this->login, $this->password);
    }
}