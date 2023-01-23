<?php

namespace Glsv\DiadocApi\helpers;

use Glsv\DiadocApi\exceptions\DiadocRuntimeApiException;
use Psr\Http\Message\ResponseInterface;

class FilenameResponseGetter
{
    public static function getFilename(ResponseInterface $response): string
    {
        $contentDisposition = $response->getHeader('Content-Disposition');
        if (empty($contentDisposition)) {
            throw new DiadocRuntimeApiException('Content-Disposition doesn`t present in response headers');
        }

        if (preg_match('|filename="([^"]+)"|is', $contentDisposition[0], $matches) === false) {
            throw new DiadocRuntimeApiException('filename doesn`t present in Content-Disposition header');
        }

        return $matches[1];
    }
}