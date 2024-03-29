<?php

namespace WireMock\Client;

use Phake;
use WireMock\HamcrestTestCase;
use WireMock\Http\ResponseDefinition;
use WireMock\Matching\RequestPattern;

class MappingBuilderTest extends HamcrestTestCase
{
    private $_mockRequestPatternBuilder;
    private $_mockResponseDefinitionBuilder;

    public function setUp(): void
    {
        $mockRequestPatternBuilder = Phake::mock(RequestPatternBuilder::class);
        $mockRequestPattern = Phake::mock(RequestPattern::class);
        Phake::when($mockRequestPatternBuilder)->build()->thenReturn($mockRequestPattern);
        $this->_mockRequestPatternBuilder = $mockRequestPatternBuilder;

        $mockResponseDefinition = Phake::mock(ResponseDefinition::class);

        $mockResponseDefinitionBuilder = Phake::mock(ResponseDefinitionBuilder::class);
        Phake::when($mockResponseDefinitionBuilder)->build()->thenReturn($mockResponseDefinition);
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
        assertThat($stubMapping->getRequest(), notNullValue());
        assertThat($stubMapping->getResponse(), notNullValue());
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
        Phake::verify($this->_mockRequestPatternBuilder)->withHeader($headerName, $valueMatchingStrategy);
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
        Phake::verify($this->_mockRequestPatternBuilder)->withQueryParam($paramName, $valueMatchingStrategy);
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
        Phake::verify($this->_mockRequestPatternBuilder)->withRequestBody($valueMatchingStrategy);
    }
}
