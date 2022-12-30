<?php

declare(strict_types=1);

namespace Glsv\DiadocApi\requests;

use Glsv\DiadocApi\interfaces\RequestInterface;
use Glsv\DiadocApi\vo\RequestMethod;

class GetDocumentRequest implements RequestInterface
{
    public string $boxId;
    public string $messageId;
    public string $documentId;
    public $injectEntityContent = false;

    public function __construct(string $boxId, string $messageId, string $documentId)
    {
        $this->boxId = $boxId;
        $this->messageId = $messageId;
        $this->documentId = $documentId;
    }

    public function buildBody(): array
    {
        return [
            'boxId' => $this->boxId,
            'messageId' => $this->messageId,
            'entityId' => $this->documentId,
            'injectEntityContent' => $this->injectEntityContent ? 'true' : 'false',
        ];
    }

    public function getMethod(): RequestMethod
    {
        return new RequestMethod(RequestMethod::METHOD_GET);
    }

    public function getUrl(): string
    {
        return '/V3/GetDocument';
    }
}