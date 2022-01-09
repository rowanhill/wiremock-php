<?php

namespace WireMock\Client;

use WireMock\Http\ResponseDefinition;
use WireMock\Stubbing\StubMapping;

class ServeEvent
{
    /** @var string */
    private $id;
    /** @var LoggedRequest */
    private $request;
    /** @var StubMapping */
    private $stubMapping;
    /** @var ResponseDefinition */
    private $responseDefinition;
    /** @var LoggedResponse */
    private $response;

    /**
     * @param string $id
     * @param LoggedRequest $request
     * @param StubMapping $stubMapping
     * @param ResponseDefinition $responseDefinition
     * @param LoggedResponse $response
     */
    public function __construct(
        $id,
        LoggedRequest $request,
        StubMapping $stubMapping,
        ResponseDefinition $responseDefinition,
        LoggedResponse $response
    ) {
        $this->id = $id;
        $this->request = $request;
        $this->stubMapping = $stubMapping;
        $this->responseDefinition = $responseDefinition;
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return LoggedRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return StubMapping
     */
    public function getStubMapping()
    {
        return $this->stubMapping;
    }

    /**
     * @return ResponseDefinition
     */
    public function getResponseDefinition()
    {
        return $this->responseDefinition;
    }

    /**
     * @return LoggedResponse
     */
    public function getResponse()
    {
        return $this->response;
    }
}