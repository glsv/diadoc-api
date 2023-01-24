<?php

declare(strict_types=1);

namespace Glsv\DiadocApi\usecases;

use Glsv\DiadocApi\commands\GeneratePrintFormCommand;
use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\dto\FileDto;
use Glsv\DiadocApi\exceptions\DiadocRuntimeApiException;
use Glsv\DiadocApi\requests\GeneratePrintFormRequest;

class GetPrintFormDocumentUsecase
{
    private DiadocClientApi $api;
    private string $boxId;
    private string $messageId;
    private string $entityId;

    public function __construct(DiadocClientApi $api, string $boxId, string $messageId, string $entityId)
    {
        $this->api = $api;
        $this->boxId = $boxId;
        $this->messageId = $messageId;
        $this->entityId = $entityId;
    }

    public function getFile(): FileDto
    {
        $request = new GeneratePrintFormRequest($this->boxId, $this->messageId, $this->entityId);
        $result = (new GeneratePrintFormCommand($this->api, $request))->execute();

        if ($result->isError()) {
            throw new DiadocRuntimeApiException($result->getError());
        }

        $files = $result->getData();

        if (empty($files)) {
            throw new DiadocRuntimeApiException('File doesn`t received');
        }

        return array_shift($files);
    }
}