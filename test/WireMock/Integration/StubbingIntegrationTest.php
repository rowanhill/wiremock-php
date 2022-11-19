<?php

namespace WireMock\Integration;

require_once 'WireMockIntegrationTest.php';

use Exception;
use WireMock\Client\ClientException;
use WireMock\Client\DateTimeMatchingStrategy;
use WireMock\Client\WireMock;
use WireMock\Client\XmlUnitComparisonType;
use WireMock\Stubbing\StubMapping;

class StubbingIntegrationTest extends WireMockIntegrationTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->clearMappings();
    }

    public function tearDown(): void
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
        assertThat($stubMapping->getRequest()->getUrlMatchingStrategy(),
            equalTo(WireMock::urlPathEqualTo('/some/url')));
    }

    public function testRequestUrlPathCanBeMatchedByRegex()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlPathMatching('/some/.+'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getRequest()->getUrlMatchingStrategy(),
            equalTo(WireMock::urlPathMatching('/some/.+')));
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
        assertThat($stubMapping->getResponse()->getHeaders(), equalTo(array(
            'Content-Type' => 'text/plain'
        )));
    }

    public function testMultivalueHeadersCanBeStubbed()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()
                ->withHeader('Set-Cookie', 'key1=val1')
                ->withHeader('Set-Cookie', 'key2=val2')
                ->withBody('Here is some body text')));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getResponse()->getHeaders(), equalTo(array(
            'Set-Cookie' => array(
                'key1=val1',
                'key2=val2'
            )
        )));
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
            'X-Header' => WireMock::equalToIgnoreCase('VaLUe')
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
            WireMock::binaryEqualTo('AQID')
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

    public function testResponseCanBeStubbedByBodyNotContaining()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::notContaining('ERROR'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        $bodyPattern = $stubMapping->getRequest()->getBodyPatterns()[0];
        assertThat($bodyPattern->getMatchingType(), equalTo('doesNotContain'));
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
            WireMock::equalToJson('{"key":"value"}'),
            WireMock::equalToJson('{}', true, true)
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
            WireMock::matchingJsonPath('$.status'),
            WireMock::matchingJsonPath('$.status', WireMock::containing('ok'))
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

    public function testResponsesCanBeStubbedByBodyXmlWithPlaceholders()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::equalToXml('<tag>${xmlunit.ignore}</tag>', true))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByBodyXmlWithPlaceholdersAndCustomDelimiters()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(WireMock::equalToXml(
                '<tag>[[xmlunit.ignore]]</tag>',
                true,
                '\[\[',
                ']]'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByBodyXmlExemptingSpecificComparisonTypes()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withRequestBody(
                WireMock::equalToXml('<tag>Foo</tag>')
                    ->exemptingComparisons(XmlUnitComparisonType::NAMESPACE_URI, XmlUnitComparisonType::ELEMENT_TAG_NAME)
            )
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

    public function testResponsesCanBeStubbedByHostMatching()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get("/things")
            ->withHost(WireMock::equalTo("my.first.domain"))
            ->willReturn(WireMock::ok("Domain 1")));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByBeforeLiteralMatcher()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader("X-Munged-Date", WireMock::before("2021-05-01T00:00:00Z"))
            ->willReturn(WireMock::ok()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByBeforeNowMatcher()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader("X-Munged-Date", WireMock::beforeNow())
            ->willReturn(WireMock::ok()));

        // then
        // The server will modify "now" to "now +0 seconds", so we change the locally generated stub mapping to be the
        // same before asserting
        $matchingStrat = $stubMapping->getRequest()->getHeaders()['X-Munged-Date'];
        $matchValProp = new \ReflectionProperty($matchingStrat, 'matchingValue');
        $matchValProp->setAccessible(true);
        $matchValProp->setValue($matchingStrat, 'now +0 seconds');
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByEqualToDateTimeLiteralMatcher()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader("X-Munged-Date", WireMock::equalToDateTime("2021-05-01T00:00:00Z"))
            ->willReturn(WireMock::ok()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByIsNowMatcher()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader("X-Munged-Date", WireMock::isNow())
            ->willReturn(WireMock::ok()));

        // then
        // The server will modify "now" to "now +0 seconds", so we change the locally generated stub mapping to be the
        // same before asserting
        $matchingStrat = $stubMapping->getRequest()->getHeaders()['X-Munged-Date'];
        $matchValProp = new \ReflectionProperty($matchingStrat, 'matchingValue');
        $matchValProp->setAccessible(true);
        $matchValProp->setValue($matchingStrat, 'now +0 seconds');
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByAfterLiteralMatcher()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader("X-Munged-Date", WireMock::after("2021-05-01T00:00:00Z"))
            ->willReturn(WireMock::ok()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByAfterNowMatcher()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader("X-Munged-Date", WireMock::afterNow())
            ->willReturn(WireMock::ok()));

        // then
        // The server will modify "now" to "now +0 seconds", so we change the locally generated stub mapping to be the
        // same before asserting
        $matchingStrat = $stubMapping->getRequest()->getHeaders()['X-Munged-Date'];
        $matchValProp = new \ReflectionProperty($matchingStrat, 'matchingValue');
        $matchValProp->setAccessible(true);
        $matchValProp->setValue($matchingStrat, 'now +0 seconds');
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByDateTimeMatcherWithOffset()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader("X-Munged-Date", WireMock::after("now")
                ->expectedOffset(3, DateTimeMatchingStrategy::DAYS))
            ->willReturn(WireMock::ok()));

        // then
        // The server will return the offset in the matching value string, so we change the locally generated stub
        // mapping to be the same before asserting
        $matchingStrat = $stubMapping->getRequest()->getHeaders()['X-Munged-Date'];
        $matchValProp = new \ReflectionProperty($matchingStrat, 'matchingValue');
        $matchValProp->setAccessible(true);
        $matchValProp->setValue($matchingStrat, 'now +3 days');
        $expectedOffsetProp = new \ReflectionProperty($matchingStrat, 'expectedOffset');
        $expectedOffsetProp->setAccessible(true);
        $expectedOffsetProp->setValue($matchingStrat, null);
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponsesCanBeStubbedByDateTimeMatcherWithOffsetInStringSpec()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader("X-Munged-Date", WireMock::after("now +5 months"))
            ->willReturn(WireMock::ok()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByDateTimeMatcherWithActualFormat()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader(
                "X-Munged-Date",
                WireMock::equalToDateTime("2021-05-01T00:00:00Z")
                    ->actualFormat("dd/MM/yyyy")
            )
            ->willReturn(WireMock::ok()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByDateTimeMatcherWithTruncatedExpected()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader(
                "X-Munged-Date",
                WireMock::equalToDateTime("2021-05-01T00:00:00Z")
                    ->truncateExpected(DateTimeMatchingStrategy::FIRST_DAY_OF_MONTH)
            )
            ->willReturn(WireMock::ok()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByDateTimeMatcherWithTruncatedActual()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::post(WireMock::urlEqualTo('/some/url'))
            ->withHeader(
                "X-Munged-Date",
                WireMock::after("now +15 days")
                    ->truncateActual(DateTimeMatchingStrategy::FIRST_DAY_OF_MONTH)
            )
            ->willReturn(WireMock::ok()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByLogicalAndOfMatchersFromStaticMethod()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlPathEqualTo("/and"))
            ->withHeader("X-Some-Value", WireMock::and(
                WireMock::matching("[a-z]+"),
                WireMock::containing("magicvalue")
            ))
            ->willReturn(WireMock::ok()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByLogicalAndOfChainedMatchers()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlPathEqualTo("/and"))
            ->withHeader(
                "X-Some-Value",
                WireMock::matching("[a-z]+")->and(WireMock::containing("magicvalue"))
            )
            ->willReturn(WireMock::ok()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByLogicalOrOfMatchersFromStaticMethod()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlPathEqualTo("/or"))
            ->withQueryParam("search", WireMock::or(
                WireMock::matching("[a-z]+"),
                WireMock::absent()
            ))
            ->willReturn(WireMock::ok()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testResponseCanBeStubbedByLogicalOrOfChainedMatchers()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlPathEqualTo("/or"))
            ->withQueryParam(
                "search",
                WireMock::matching("[a-z]+")->or(WireMock::absent())
            )
            ->willReturn(WireMock::ok()));

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

    public function testStubIdIsReturnedInResponseHeader()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withId('76ada7b0-49ae-4229-91c4-396a36f18e09')
            ->willReturn(WireMock::aResponse()));

        // when
        $result = $this->_testClient->get('/some/url', array(), true);

        // then
        assertThat($result, containsString('Matched-Stub-Id: 76ada7b0-49ae-4229-91c4-396a36f18e09'));
    }

    public function testStubNameCanBeSet()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withName('stub-name')
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStubNameIsReturnedInResponseHeader()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withName('stub-name')
            ->willReturn(WireMock::aResponse()));

        // when
        $result = $this->_testClient->get('/some/url', array(), true);

        // then
        assertThat($result, containsString('Matched-Stub-Name: stub-name'));
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

    public function testResponseTransformersCanBeSetOnStub()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(
                WireMock::aResponse()
                    ->withTransformers('transformer-1', 'transformer-2')
            ));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getResponse()->getTransformers(), equalTo(array('transformer-1', 'transformer-2')));
    }

    public function testResponseTransformerParametersCanBeSetOnStub()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(
                WireMock::aResponse()
                    ->withTransformerParameter('newValue', 66)
                    ->withTransformerParameter('inner', array('thing' => 'value'))
            ));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getResponse()->getTransformerParameters(), equalTo(array(
            'newValue' => 66,
            'inner' => array('thing' => 'value')
        )));
    }

    public function testResponseTransformerAndParameterCanBeSetOnStubInOneGo()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(
                WireMock::aResponse()
                    ->withTransformer('transformer-1', 'newValue', 66)
            ));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getResponse()->getTransformers(), equalTo(array('transformer-1')));
        assertThat($stubMapping->getResponse()->getTransformerParameters(), equalTo(array('newValue' => 66)));
    }

    public function testStubsCanBeCreatedWithCustomMatchers()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(
            WireMock::requestMatching('custom-matcher', array('param' => 'val'))
                ->willReturn(WireMock::aResponse())
        );

        // then
        // Locally created stub mapping will have null request method, but version returned from server will
        // have that defaulted to ANY. Our public API doesn't allow that modification, so we make some cheeky
        // changes via reflection before asserting.
        $methodProp = new \ReflectionProperty($stubMapping->getRequest(), 'method');
        $methodProp->setAccessible(true);
        $methodProp->setValue($stubMapping->getRequest(), 'ANY');
        assertThatTheOnlyMappingPresentIs($stubMapping);
        $customMatcher = $stubMapping->getRequest()->getCustomMatcher();
        assertThat($customMatcher->getName(), is('custom-matcher'));
        assertThat($customMatcher->getParameters(), is(array('param' => 'val')));
    }

    public function testStubsCanBeCreatedWithStandardMatchersCombinedWithCustomMatchers()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(
            WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->andMatching('custom-matcher', array('param' => 'val'))
                ->willReturn(WireMock::aResponse())
        );

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        $customMatcher = $stubMapping->getRequest()->getCustomMatcher();
        assertThat($customMatcher->getName(), is('custom-matcher'));
        assertThat($customMatcher->getParameters(), is(array('param' => 'val')));
    }

    public function testStubsCanBeCreatedWithMetadataAttached()
    {
        // when
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->withMetadata(array('meta' => 'data'))
            ->willReturn(WireMock::aResponse()));

        // then
        assertThatTheOnlyMappingPresentIs($stubMapping);
        assertThat($stubMapping->getMetadata(), is(array('meta' => 'data')));
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
        // The stub mapping will be marked as persistent by the server, so we update the locally created version to
        // match before asserting
        $persistentProp = new \ReflectionProperty($stubMapping, 'persistent');
        $persistentProp->setAccessible(true);
        $persistentProp->setValue($stubMapping, true);
        assertThatTheOnlyMappingPresentIs($stubMapping);
    }

    public function testStubsCanBeImmediatelyPersisted()
    {
        // given
        $stubMapping = self::$_wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
            ->willReturn(WireMock::aResponse()->withBody('Here is some body text'))
            ->persistent()
        );

        // when
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

    /**
     * @throws Exception
     */
    public function testStubsCanBeImported()
    {
        // when
        self::$_wireMock->importStubs(WireMock::stubImport()
            ->stub(WireMock::get('/one')->willReturn(WireMock::ok()))
            ->stub(WireMock::post('/two')->willReturn(WireMock::ok("Body content")))
            ->stub(WireMock::put('/three')->willReturn(WireMock::ok()))
        );

        // then
        $mappings = getMappings();
        $urls = array_map(function(/** @var $m StubMapping */$m) {
            return $m->getRequest()->getUrlMatchingStrategy()->getMatchingValue();
        },
            $mappings);
        assertThat($urls, arrayContainingInAnyOrder(array('/one', '/two', '/three')));
    }

    /**
     * @throws Exception
     */
    public function testStubImportOverwritesStubsByDefault()
    {
        // given
        $id = '76ada7b0-49ae-4229-91c4-396a36f18e09';
        self::$_wireMock->stubFor(
            WireMock::get('/path')->withId($id)->willReturn(WireMock::ok())
        );

        // when
        self::$_wireMock->importStubs(WireMock::stubImport()->stub(
            WireMock::get('/path2')->withId($id)->willReturn(WireMock::ok())
        ));

        // then
        $mappings = getMappings();
        assertThat($mappings, is(arrayWithSize(1)));
        $mapping = $mappings[0];
        assertThat($mapping->getRequest()->getUrlMatchingStrategy()->getMatchingValue(), is('/path2'));
    }

    /**
     * @throws Exception
     */
    public function testStubImportCanBeSetToIgnoreDuplicates()
    {
        // given
        $id = '76ada7b0-49ae-4229-91c4-396a36f18e09';
        self::$_wireMock->stubFor(
            WireMock::get('/path')->withId($id)->willReturn(WireMock::ok())
        );

        // when
        self::$_wireMock->importStubs(WireMock::stubImport()->stub(
            WireMock::get('/path2')->withId($id)->willReturn(WireMock::ok())
        )->ignoreExisting());

        // then
        $mappings = getMappings();
        assertThat($mappings, is(arrayWithSize(1)));
        $mapping = $mappings[0];
        assertThat($mapping->getRequest()->getUrlMatchingStrategy()->getMatchingValue(), is('/path'));
    }

    /**
     * @throws Exception
     */
    public function testStubImportLeavesExistingStubsInPlaceByDefault()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get('/path')->willReturn(WireMock::ok()));

        // when
        self::$_wireMock->importStubs(WireMock::stubImport()
            ->stub(WireMock::get('/one')->willReturn(WireMock::ok()))
            ->stub(WireMock::post('/two')->willReturn(WireMock::ok()))
        );

        // then
        $mappings = getMappings();
        assertThat($mappings, is(arrayWithSize(3)));
        $urls = array_map(function(/** @var $m StubMapping */$m) {
                return $m->getRequest()->getUrlMatchingStrategy()->getMatchingValue();
            },
            $mappings);
        assertThat($urls, arrayContainingInAnyOrder(array('/path', '/one', '/two')));
    }

    /**
     * @throws Exception
     */
    public function testStubImportCanBeSetToReplaceAnyExistingStubs()
    {
        // given
        self::$_wireMock->stubFor(WireMock::get('/path')->willReturn(WireMock::ok()));

        // when
        self::$_wireMock->importStubs(WireMock::stubImport()
            ->stub(WireMock::get('/one')->willReturn(WireMock::ok()))
            ->stub(WireMock::post('/two')->willReturn(WireMock::ok()))
            ->deleteAllExistingStubsNotInImport()
        );

        // then
        $mappings = getMappings();
        assertThat($mappings, is(arrayWithSize(2)));
        $urls = array_map(function(/** @var $m StubMapping */$m) {
            return $m->getRequest()->getUrlMatchingStrategy()->getMatchingValue();
        },
            $mappings);
        assertThat($urls, arrayContainingInAnyOrder(array('/one', '/two')));
    }

    /**
     * @throws Exception
     */
    public function testStubImportWithMalformedIdThrowsExceptionFromWiremock()
    {
        // given
        $stub = WireMock::get('/one')->willReturn(WireMock::ok());
        $stub->withId('not-a-uuid');

        // then
        $this->expectException(ClientException::class);

        // when
        self::$_wireMock->importStubs(WireMock::stubImport()
            ->stub($stub));
    }
}
