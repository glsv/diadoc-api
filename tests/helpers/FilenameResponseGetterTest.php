<?php

namespace helpers;

use Glsv\DiadocApi\exceptions\DiadocRuntimeApiException;
use Glsv\DiadocApi\helpers\FilenameResponseGetter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class FilenameResponseGetterTest extends TestCase
{
    public function testSuccessGet()
    {
        $filename = "4fb9ac6e-c32e-4057-9a10-6fdb0092aea5.ec164dfb-bc95-442f-8f60-e34ee51e5c15.638061937254976253_ru_.pdf";
        $header = "attachment; filename=\"$filename\"";

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->method('getHeader')->willReturn([$header]);

        $result = FilenameResponseGetter::getFilename($response);

        $this->assertSame($filename, $result);
    }

    public function testEmptyGet()
    {
        $this->expectException(DiadocRuntimeApiException::class);

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->method('getHeader')->willReturn([]);

        FilenameResponseGetter::getFilename($response);
    }
}
