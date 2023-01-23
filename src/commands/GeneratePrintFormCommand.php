<?php

declare(strict_types=1);

namespace Glsv\DiadocApi\commands;

use Glsv\DiadocApi\DiadocClientApi;
use Glsv\DiadocApi\interfaces\ApiResponseInterface;
use Glsv\DiadocApi\interfaces\CommandInterface;
use Glsv\DiadocApi\requests\GeneratePrintFormRequest;
use Glsv\DiadocApi\services\CommandExecutor;
use Glsv\DiadocApi\vo\RequestMethod;

class GeneratePrintFormCommand  implements CommandInterface
{
    protected $url = '/GeneratePrintForm';
    protected DiadocClientApi $api;
    protected GeneratePrintFormRequest $request;

    public function __construct(DiadocClientApi $api, GeneratePrintFormRequest $request)
    {
        $this->api = $api;
        $this->request = $request;
    }

    /**
     * При успешном выполнении возвращается SuccessFileResponse()
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