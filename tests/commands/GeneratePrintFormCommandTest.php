<?php

namespace commands;

use Glsv\DiadocApi\commands\GeneratePrintFormCommand;
use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\dto\FileDto;
use Glsv\DiadocApi\requests\GeneratePrintFormRequest;
use Glsv\DiadocApi\responses\ErrorResponse;
use Glsv\DiadocApi\responses\SuccessFileResponse;
use PHPUnit\Framework\TestCase;

class GeneratePrintFormCommandTest extends TestCase
{
    public function testSuccessExecute()
    {
        $expectedResponse = new SuccessFileResponse(
            new FileDto('data', 'application/pdf', 'file.pdf')
        );

        $api = $this->getMockBuilder(DiadocClientApi::class)->disableOriginalConstructor()->getMock();
        $api->method('executeGet')->willReturn($expectedResponse);

        $command = new GeneratePrintFormCommand(
            $api, new GeneratePrintFormRequest('box_id', 'message_id', 'doc_id')
        );

        $response = $command->execute();

        $this->assertSame($expectedResponse, $response);
    }

    public function testFailExecute()
    {
        $expectedResponse = new ErrorResponse(400, 'not found');

        $api = $this->getMockBuilder(DiadocClientApi::class)->disableOriginalConstructor()->getMock();
        $api->method('executeGet')->willReturn($expectedResponse);

        $command = new GeneratePrintFormCommand(
            $api, new GeneratePrintFormRequest('box_id', 'message_id', 'doc_id')
        );

        $response = $command->execute();

        $this->assertSame($expectedResponse, $response);
    }
}
