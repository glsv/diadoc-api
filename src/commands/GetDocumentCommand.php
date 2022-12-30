<?php

namespace Glsv\DiadocApi\commands;

use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\requests\GetDocumentRequest;
use Glsv\DiadocApi\services\CommandExecutor;
use Glsv\DiadocApi\vo\RequestMethod;

class GetDocumentCommand
{
    protected $url = '/V3/GetDocument';
    protected $api;
    protected $request;

    public function __construct(DiadocClientApi $api, GetDocumentRequest $request)
    {
        $this->api = $api;
        $this->request = $request;
    }

    public function execute()
    {
        return (new CommandExecutor($this->api))->executeRequest(
            new RequestMethod(RequestMethod::METHOD_GET), $this->url, $this->request
        );
    }
}