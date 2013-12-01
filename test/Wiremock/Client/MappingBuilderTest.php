<?php

namespace WireMock\Client;

use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;

class MappingBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var RequestPattern */
    private $_mockRequestPattern;
    /** @var ResponseDefinitionBuilder */
    private $_mockResponseDefinitionBuilder;
    /** @var ResponseDefinition */
    private $_mockResponseDefinition;

    function setUp()
    {
        /** @var RequestPattern $mockRequestPattern */
        $mockRequestPattern = mock('WireMock\Matching\RequestPattern');
        when($mockRequestPattern->toArray())->return(array('aRequest' => 'pattern'));
        $this->_mockRequestPattern = $mockRequestPattern;

        /** @var ResponseDefinition $mockResponseDefinition */
        $mockResponseDefinition = mock('WireMock\Http\ResponseDefinition');
        when($mockResponseDefinition->toArray())->return(array('aResponse' => 'definition'));
        $this->_mockResponseDefinition = $mockResponseDefinition;

        /** @var ResponseDefinitionBuilder $mockResponseDefinitionBuilder */
        $mockResponseDefinitionBuilder = mock('WireMock\Client\ResponseDefinitionBuilder');
        when($mockResponseDefinitionBuilder->build())->return($mockResponseDefinition);
        $this->_mockResponseDefinitionBuilder = $mockResponseDefinitionBuilder;
    }

    function testBuiltStubMappingHasRequestPatternAndResponseDefinition()
    {
        // given
        $mappingBuilder = new MappingBuilder($this->_mockRequestPattern);
        $mappingBuilder->willReturn($this->_mockResponseDefinitionBuilder);

        // when
        $stubMapping = $mappingBuilder->build();

        // then
        $array = $stubMapping->toArray();
        assertThat($array, hasEntry('request', array('aRequest' => 'pattern')));
        assertThat($array, hasEntry('response', array('aResponse' => 'definition')));
    }

    function testMatchedRequestHeadersAreSetOnRequestPattern()
    {
        // given
        $mappingBuilder = new MappingBuilder($this->_mockRequestPattern);
        $mappingBuilder->withHeader('aHeader', new ValueMatchingStrategy('equalTo', 'aValue'));
        $mappingBuilder->willReturn($this->_mockResponseDefinitionBuilder);

        // when
        $mappingBuilder->build();

        // then
        $headers = array('aHeader' => array('equalTo' => 'aValue'));
        verify($this->_mockRequestPattern)->setHeaders($headers);
    }
}