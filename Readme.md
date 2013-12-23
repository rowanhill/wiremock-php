wiremock-php
============
A PHP API for [WireMock](https://github.com/tomakehurst/wiremock).

WireMock provides a JSON API for interacting with it; wiremock-php makes it easy to use that JSON API from PHP by
wrapping it up in a fluent API very similar to the Java API provided by WireMock.

Usage
-----
The API is based directly on WireMock's Java API, so see the [WireMock documentation](http://wiremock.org/) for general
help with interacting with WireMock.

Unfortunately, PHP doesn't support anything like Java's static import of methods, so the result is slightly less pretty
than the Java API. Most methods are static on the WireMock class, but methods which are static in Java are instance
methods in PHP. Those methods are:

- stubFor
- verify
- findAll
- reset
- isAlive (not part of the WireMock API, used to check if the standalone service is up and running)

Also, Java has an overload of withBody() that takes a byte array. Byte arrays are less common in PHP, so instead,
`withBodyData()` is provided, which takes a string to base64 encoded. To produce an appropriate string from an array
of bytes, use [pack](http://php.net/pack).

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

If a verification fails a `VerificationException` is thrown. As this is a subclass of
`PHPUnit_Framework_AssertionFailedError`, this will cause any containing PHPUnit test to fail.

TODO
----
The whole WireMock API is not yet supported. In particular, the following features are missing:

- Stateful behaviour (`inScenario()`, `whenScenarioStateIs()`, `willSetStateTo()`)
- Proxying (`proxiedFrom()`)
- Delays (`withFixedDelay()`, `setGlobalFixedDelay()`, `addRequestProcessingDelay()`)
- Faults (`withFault()`)

None of this should be particularly difficult to add, merely time consuming - pull requests very welcome!

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/rowanhill/wiremock-php/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
