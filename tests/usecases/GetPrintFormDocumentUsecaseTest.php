<?php

namespace usecases;

use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\dto\FileDto;
use Glsv\DiadocApi\exceptions\DiadocRuntimeApiException;
use Glsv\DiadocApi\responses\ErrorResponse;
use Glsv\DiadocApi\responses\SuccessFileResponse;
use Glsv\DiadocApi\responses\SuccessResponse;
use Glsv\DiadocApi\usecases\GetPrintFormDocumentUsecase;
use PHPUnit\Framework\TestCase;

class GetPrintFormDocumentUsecaseTest extends TestCase
{
    public function testSuccess()
    {
        $expectedResponse = new SuccessFileResponse(
            new FileDto('data', 'application/pdf', 'file.pdf')
        );

        $api = $this->getMockBuilder(DiadocClientApi::class)->disableOriginalConstructor()->getMock();
        $api->method('executeGet')->willReturn($expectedResponse);

        $usecase = new GetPrintFormDocumentUsecase($api, 'box_id', 'message_id', 'doc_id');
        $file = $usecase->getFile();

        $this->assertSame($expectedResponse->getData()[0], $file);
    }

    public function testFail()
    {
        $this->expectException(DiadocRuntimeApiException::class);

        $expectedResponse = new ErrorResponse(400, 'error');

        $api = $this->getMockBuilder(DiadocClientApi::class)->disableOriginalConstructor()->getMock();
        $api->method('executeGet')->willReturn($expectedResponse);

        $usecase = new GetPrintFormDocumentUsecase($api, 'box_id', 'message_id', 'doc_id');
        $usecase->getFile();
    }

    public function testNoFile()
    {
        $this->expectException(DiadocRuntimeApiException::class);

        $expectedResponse = new SuccessResponse([]);

        $api = $this->getMockBuilder(DiadocClientApi::class)->disableOriginalConstructor()->getMock();
        $api->method('executeGet')->willReturn($expectedResponse);

        $usecase = new getPrintFormDocumentUsecase($api, 'box_id', 'message_id', 'doc_id');
        $usecase->getFile();
    }
}
