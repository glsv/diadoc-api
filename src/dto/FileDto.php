<?php

namespace Glsv\DiadocApi\dto;

class FileDto
{
    public string $binaryData;
    public string $filename;
    public string $contentType;

    public function __construct(string $binaryData, string $contentType, string $filename)
    {
        $this->binaryData = $binaryData;
        $this->filename = $filename;
        $this->contentType = $contentType;
    }
}