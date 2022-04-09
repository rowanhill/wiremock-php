wiremock-php [![Build Status](https://travis-ci.org/rowanhill/wiremock-php.png?branch=master)](https://travis-ci.org/rowanhill/wiremock-php)
============
**Stub and mock web services with the power of [WireMock](https://github.com/tomakehurst/wiremock) from PHP.**

WireMock provides a JSON API for interacting with it; wiremock-php makes it easy to use that JSON API from PHP by
wrapping it up in a fluent API very similar to the Java API provided by WireMock.

Note that wiremock-php requires a standalone instance of WireMock to be run (which requires Java).

Version numbers track those of WireMock itself, but may lag behind (i.e. if a WireMock release does not contain changes
to the API, there may be no corresponding version of wiremock-php).

Alternatives
------------
Like the idea of stubbing & verifying HTTP requests, but don't like this library (maybe you don't want to install Java)?
You might want to investigate the following:
* [http-mock](https://github.com/InterNations/http-mock)
* [mock-http-server](https://github.com/cepa/mock-http-server)

Installation
------------
It's easiest to install wiremock-php via Composer:

```bash
php composer.phar require --dev wiremock-php/wiremock-php:2.33.1
```

Usage
-----
### API
The API is based directly on WireMock's Java API, so see the [WireMock documentation](http://wiremock.org/) for general
help with interacting with WireMock.

### Differences to Java API
To provide a fluent interface, the WireMock Java API makes use of statically imported methods (which act upon a default
static instance), but it's also possible to act directly upon a Java WireMock instance (using slightly differently
named methods).

Prior to version 5.6 (back when wiremock-php was created), PHP didn't support anything like Java's static import of
methods. Instead, in wiremock-php some methods which are static in Java are instance methods. Those methods are:

- `stubFor`, `editStub`, `importStubs`
- `verify`
- `get`, `post`, `put`, `delete`, `patch`, `head`, `options`, `trace`, `any`
- all the various matcher methods
- `getAllServeEvents`, `findAll`, `findUnmatchedRequests`, `findNearMissesFor`, `findNearMissesForAllUnmatched`
- `listAllStubMappings`, `getSingleStubMapping`, `findStubsByMetadata`, `removeStubsByMetadata`
- `saveAllMappings`
- `reset`, `resetToDefault`
- `resetAllRequests`, `removeServeEvent`, `removeServeEvents`, `removeEventsByStubMetadata`
- `getAllScenarios`, `resetAllScenarios`, `resetScenario`, `setScenarioState`
- `setGlobalFixedDelay`, `setGlobalRandomDelay`, `resetGlobalDelays`
- `startRecording`, `getRecordingStatus`, `stopRecording`, `snapshotRecord`
- `shutdownServer`

Also, the Java API has methods (`ResponseDefinitionBuilder::withBody` and `WebhookDefinition::withBinaryBody`) that take
a byte array. Byte arrays are less common in PHP, so instead, `withBodyData` methods are provided, which takes a string
to be base64 encoded. To produce an appropriate string from an array of bytes, use [pack](http://php.net/pack).

The date and time request matcher functions (`before`, `beforeNow`, `equalToDateTime`, `isNow`, `after`, `afterNow`) can
be used with offsets (`expectedOffset($amount, $unit)`). In the Java API, the unit parameter is an enum; in wiremock-php
these values are consts on `DateTimeMatchingStrategy`. Similarly, truncation types (for use with `truncateExpected` and
`truncateActual`) are enums in the Java API, but are consts on `DateTimeMatchingStrategy` in wiremock-php.

The `stubImport` method is static on `StubBuilder` in Java. In WireMock, to keep all the public static methods in one
predictable place, this method is available as `WireMock::stubImport`.

The request method constants are available on `WireMock\Http\RequestMethod`;

In addition, wiremock-php adds the instance method `isAlive`. This polls the standalone WireMock instance until an OK
response is received or a timeout is reached, allowing your PHP code to wait until WireMock is ready.

### Example
A typical usage looks something like the following:
```php
// Create an object to administer a WireMock instance. This is assumed to be at
// localhost:8080 unless these values are overridden.
$wireMock = WireMock::create(/* specify host, port here if needed */);

// Assert that the standalone service is running (by waiting for it to respond
// to a request within a timeout)
assertThat($wireMock->isAlive(), is(true));

// Stub out a request
$wireMock->stubFor(WireMock::get(WireMock::urlEqualTo('/some/url'))
    ->willReturn(WireMock::aResponse()
        ->withHeader('Content-Type', 'text/plain')
        ->withBody('Hello world!')));

// ... interact with the server ...

// Verify a request
$wireMock->verify(WireMock::postRequestedFor(WireMock::urlEqualTo('/verify/this'))
    ->withHeader('Content-Type', WireMock::equalTo('text/xml')));
```

### Verification PHPUnit integration
If a verification fails a `VerificationException` is thrown. If PHPUnit is present on the include path, this will be a
subclass of `PHPUnit_Framework_AssertionFailedError`, thus causing any containing PHPUnit test to fail; if PHPUnit is
not present, `VerificationException` subclasses `Exception`.
