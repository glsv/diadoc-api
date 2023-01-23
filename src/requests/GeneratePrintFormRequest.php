<?php

namespace Glsv\DiadocApi\requests;

use Glsv\DiadocApi\exceptions\DiadocInvalidParamsException;
use Glsv\DiadocApi\interfaces\RequestInterface;

class GeneratePrintFormRequest implements RequestInterface
{
    public string $boxId;
    public string $messageId;
    public string $documentId;

    public function __construct(string $boxId, string $messageId, string $documentId)
    {
        if ($boxId === "") {
            throw new DiadocInvalidParamsException('boxId is empty');
        }

        if ($messageId === "") {
            throw new DiadocInvalidParamsException('messageId is empty');
        }

        if ($documentId === "") {
            throw new DiadocInvalidParamsException('documentId is empty');
        }

        $this->boxId = $boxId;
        $this->messageId = $messageId;
        $this->documentId = $documentId;
    }

    public function buildBody(): array
    {
        return [
            'boxId' => $this->boxId,
            'messageId' => $this->messageId,
            'documentId' => $this->documentId,
        ];
    }
}