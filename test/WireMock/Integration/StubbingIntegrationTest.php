<?php

namespace WireMock\Integration;

require_once 'WireMockIntegrationTest.php';

use WireMock\Client\WireMock;

class StubbingIntegrationTest extends WireMockIntegrationTest
{
    public function setUp()
    {
        parent::setUp();
        $this->clearMappings();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->clearMappings();
    }

    public function testRequestWithUrlAndStringBodyCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBody('Here is some body text')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testRequestUrlAndBodyFileCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBodyFile('someFile.html')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testRequestWithBinaryBodyCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBodyData('some binary data as a string')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testRequestUrlCanBeMatchedByRegex()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlMatching('/some/.+'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testRequestCanBeMatchedByUrlPathEquality()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlPathEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getRequest()->getUrlMatchingStrategy()->toArray(),
            equalTo(array('urlPath' => '/some/url')));
    }

    public function testRequestUrlPathCanBeMatchedByRegex()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlPathMatching('/some/.+'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getRequest()->getUrlMatchingStrategy()->toArray(),
            equalTo(array('urlPathPattern' => '/some/.+')));
    }

    public function testRequestUrlCanByMatchedAsAnyUrl()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::any(WireMock::anyUrl())
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testMappingsCanBeReset()
    {
        // given
        $wiremock = WireMock::create();
        $wiremock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()));

        // when
        $wiremock->reset();

        // then
        assertThatThereAreNoMappings();
    }

    public function testMappingsCanBeResetToDefault()
    {
        // given
        $wiremock = WireMock::create();
        $wiremock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()));

        // when
        $wiremock->resetToDefault();

        // then
        assertThatThereAreNoMappings();
    }

    public function testHeadersCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withHeader('Content-Type', 'text/plain')
                ->withBody('Here is some body text')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testHeadersCanBeMatched()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withHeader('X-Header', WireMock::equalTo('value'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testAbsentHeadersCanBeMatched()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withHeader('X-Header', WireMock::absent())
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testCookiesCanBeMatched()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withCookie('foo', WireMock::equalTo('bar'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testAbsentCookiesCanBeMatched()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withCookie('foo', WireMock::absent())
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testQueryParamsCanBeMatched()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withQueryParam('foo', WireMock::equalTo('bar'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testAbsentQueryParamsCanBeMatched()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withQueryParam('foo', WireMock::absent())
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByBasicAuthMatching()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withBasicAuth('uname', 'pword')
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStatusCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withStatus(403)));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStatusMessageCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withStatusMessage("hello")));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStubAttributesCanBeMatchedByCaseInsensitiveEquality()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withHeader('X-Header', WireMock::equalToIgnoreCase('VaLUe'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getRequest()->getHeaders(), equalTo(array(
            'X-Header' => array(
                'equalTo' => 'VaLUe',
                'caseInsensitive' => true
            )
        )));
    }

    public function testStubAttributesCanBeMatchedByBase64BinaryEquality()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::binaryEqualTo('AQID'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getRequest()->getBodyPatterns(), equalTo(array(
            array(
                'binaryEqualTo' => 'AQID'
            )
        )));
    }

    public function testResponseCanBeStubbedByBodyMatching()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::matching('<status>OK</status>'))
            ->withRequestBody(WireMock::notMatching('.*ERROR.*'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByBodyContaining()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::containing('ERROR'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByBodyEqualingJson()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::equalToJson('{"key":"value"}'))
            ->withRequestBody(WireMock::equalToJson('{}', true, true))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getRequest()->getBodyPatterns(), equalTo(array(
            array(
                'equalToJson' => '{"key":"value"}'
            ),
            array(
                'equalToJson' => '{}',
                'ignoreArrayOrder' => true,
                'ignoreExtraElements' => true
            )
        )));
    }

    public function testResponseCanBeStubbedByBodyJsonPath()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::matchingJsonPath('$.status'))
            ->withRequestBody(WireMock::matchingJsonPath('$.status', WireMock::containing('ok')))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getRequest()->getBodyPatterns(), equalTo(array(
            array(
                'matchesJsonPath' => '$.status'
            ),
            array(
                'matchesJsonPath' => array(
                    'expression' => '$.status',
                    'contains' => 'ok'
                )
            )
        )));
    }

    public function testResponsesCanBeStubbedByBodyXml()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::equalToXml('<tag>Foo</tag>'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByBodyXPath()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::matchingXPath('/todo-list[count(todo-item) = 3]'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByBodyXPathWithNamespace()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::matchingXPath('/todo-list[count(todo-item) = 3]')
                ->withXPathNamespace('nspc', 'http://name.spa.ce'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByBodyXPathWithACombinedMatcher()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::matchingXPath(
                '/todo-list[count(todo-item) = 3]',
                WireMock::containing('blah')
            ))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStubIdCanBeSet()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withId('76ada7b0-49ae-4229-91c4-396a36f18e09')
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStubPriorityCanBeSet()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->atPriority(5)
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStubsCanBeSaved()
    {
        // given
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBody('Here is some body text')));

        // when
        self::$_wireMock->saveAllMappings();
        self::tearDownAfterClass(); // shut down the server
        self::setUpBeforeClass(); // start the server again

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStubsCanBeIndividuallyDeleted()
    {
        // given
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withBody('Here is some body text')));
        $stubMapping2 = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url2'))
            ->willReturn(WireMock::aResponse()
                ->withBody('Here is some body text2')));

        // when
        self::$_wireMock->removeStub($stubMapping->getId());

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping2);
    }

    public function testStubsCanBeEdited()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withId('76ada7b0-49ae-4229-91c4-396a36f18e09')
            ->willReturn(WireMock::aResponse()
                ->withBody('Original')));

        // when
        $stubMapping = self::$_wireMock->editStub(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withId('76ada7b0-49ae-4229-91c4-396a36f18e09')
            ->willReturn(WireMock::aResponse()
                ->withBody('Modified')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }
}
