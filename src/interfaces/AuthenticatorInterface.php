<?php

namespace Glsv\DiadocApi\interfaces;

use Glsv\DiadocApi\DiadocClientApi;

interface AuthenticatorInterface
{
    public function getToken(DiadocClientApi $api): string;
}