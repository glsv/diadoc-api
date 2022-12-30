<?php

namespace Glsv\DiadocApi\commands;

use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\requests\GetDocumentRequest;
use Glsv\DiadocApi\services\CommandExecutor;
use Glsv\DiadocApi\vo\RequestMethod;

class GetDocumentCommand
{
    protected $url = '/V3/GetDocument';

    public function __construct(protected DiadocClientApi $api, protected GetDocumentRequest $request)
    {
    }

    public function execute()
    {
        return (new CommandExecutor($this->api))->executeRequest(RequestMethod::GET, $this->url, $this->request);
    }
}