<?php

namespace Glsv\DiadocApi\vo;

use Glsv\DiadocApi\exceptions\DiadocRuntimeApiException;

class RequestMethod
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';

    public static $methods = [
        self::METHOD_GET,
        self::METHOD_POST,
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::$methods)) {
            throw new DiadocRuntimeApiException('method is wrong: ' . $value);
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}