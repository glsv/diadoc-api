<?php

namespace Glsv\DiadocApi\interfaces;

interface RequestInterface
{
    public function buildBody(): array;
}