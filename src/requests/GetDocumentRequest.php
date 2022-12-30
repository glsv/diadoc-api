<?php

declare(strict_types=1);

namespace Glsv\DiadocApi\requests;

use Glsv\DiadocApi\interfaces\RequestInterface;
use Glsv\DiadocApi\vo\RequestMethod;

class GetDocumentRequest implements RequestInterface
{
    public $injectEntityContent = false;

    public function __construct(public string $boxId, public string $messageId, public string $documentId)
    {
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
        return RequestMethod::GET;
    }

    public function getUrl(): string
    {
        return '/V3/GetDocument';
    }
}