<?php

namespace Glsv\DiadocApi\commands;

use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\interfaces\ApiResponseInterface;
use Glsv\DiadocApi\interfaces\CommandInterface;
use Glsv\DiadocApi\requests\GetDocumentRequest;
use Glsv\DiadocApi\services\CommandExecutor;
use Glsv\DiadocApi\vo\RequestMethod;

class GetDocumentCommand implements CommandInterface
{
    protected $url = '/V3/GetDocument';
    protected DiadocClientApi $api;
    protected $request;

    public function __construct(DiadocClientApi $api, GetDocumentRequest $request)
    {
        $this->api = $api;
        $this->request = $request;
    }

    /**
     * Формат возвращаемых данных внутри SuccessResponse()
     * https://developer.kontur.ru/Docs/diadoc-api/proto/Document.html
     * @return ApiResponseInterface
     * @throws \Glsv\DiadocApi\exceptions\DiadocRuntimeApiException
     */
    public function execute()
    {
        return (new CommandExecutor($this->api))->executeRequest(
            new RequestMethod(RequestMethod::METHOD_GET), $this->url, $this->request
        );
    }
}