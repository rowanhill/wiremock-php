<?php

namespace WireMock\Client;

use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;

class MappingBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var RequestPatternBuilder */
    private $_mockRequestPatternBuilder;
    /** @var ResponseDefinitionBuilder */
    private $_mockResponseDefinitionBuilder;
    /** @var ResponseDefinition */
    private $_mockResponseDefinition;

    public function setUp()
    {
        /** @var RequestPatternBuilder $mockRequestPatternBuilder */
        $mockRequestPatternBuilder = mock('WireMock\Client\RequestPatternBuilder');
        /** @var RequestPattern $mockRequestPattern */
        $mockRequestPattern = mock('WireMock\Matching\RequestPattern');
        when($mockRequestPatternBuilder->build())->return($mockRequestPattern);
        when($mockRequestPattern->toArray())->return(array('aRequest' => 'pattern'));
        $this->_mockRequestPatternBuilder = $mockRequestPatternBuilder;

        /** @var ResponseDefinition $mockResponseDefinition */
        $mockResponseDefinition = mock('WireMock\Http\ResponseDefinition');
        when($mockResponseDefinition->toArray())->return(array('aResponse' => 'definition'));
        $this->_mockResponseDefinition = $mockResponseDefinition;

        /** @var ResponseDefinitionBuilder $mockResponseDefinitionBuilder */
        $mockResponseDefinitionBuilder = mock('WireMock\Client\ResponseDefinitionBuilder');
        when($mockResponseDefinitionBuilder->build())->return($mockResponseDefinition);
        $this->_mockResponseDefinitionBuilder = $mockResponseDefinitionBuilder;
    }

    /**
     * @throws \Exception
     */
    public function testBuiltStubMappingHasRequestPatternAndResponseDefinition()
    {
        // given
        $mappingBuilder = new MappingBuilder($this->_mockRequestPatternBuilder);
        $mappingBuilder->willReturn($this->_mockResponseDefinitionBuilder);

        // when
        $stubMapping = $mappingBuilder->build();

        // then
        $array = $stubMapping->toArray();
        assertThat($array, hasEntry('request', array('aRequest' => 'pattern')));
        assertThat($array, hasEntry('response', array('aResponse' => 'definition')));
    }

    /**
     * @throws \Exception
     */
    public function testMatchedRequestHeadersAreSetOnRequestPattern()
    {
        // given
        $mappingBuilder = new MappingBuilder($this->_mockRequestPatternBuilder);
        $mappingBuilder->willReturn($this->_mockResponseDefinitionBuilder);
        $headerName = 'aHeader';
        $valueMatchingStrategy = new ValueMatchingStrategy('equalTo', 'aValue');

        // when
        $mappingBuilder->withHeader($headerName, $valueMatchingStrategy)->build();

        // then
        verify($this->_mockRequestPatternBuilder)->withHeader($headerName, $valueMatchingStrategy);
    }

    /**
     * @throws \Exception
     */
    public function testMatchedRequestQueryParamsAreSetOnRequestPattern()
    {
        // given
        $mappingBuilder = new MappingBuilder($this->_mockRequestPatternBuilder);
        $mappingBuilder->willReturn($this->_mockResponseDefinitionBuilder);
        $paramName = 'aParam';
        $valueMatchingStrategy = new ValueMatchingStrategy('equalTo', 'aValue');

        // when
        $mappingBuilder->withQueryParam($paramName, $valueMatchingStrategy)->build();

        // then
        verify($this->_mockRequestPatternBuilder)->withQueryParam($paramName, $valueMatchingStrategy);
    }

    /**
     * @throws \Exception
     */
    public function testRequestBodyMatcherIsSetOnRequestPattern()
    {
        // given
        $mappingBuilder = new MappingBuilder($this->_mockRequestPatternBuilder);
        $mappingBuilder->willReturn($this->_mockResponseDefinitionBuilder);

        // when
        $valueMatchingStrategy = new ValueMatchingStrategy('matches', 'aValue');
        $mappingBuilder->withRequestBody($valueMatchingStrategy)
            ->build();

        // then
        verify($this->_mockRequestPatternBuilder)->withRequestBody($valueMatchingStrategy);
    }
}
