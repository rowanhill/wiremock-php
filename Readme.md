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
It easiest to install wiremock-php via Composer:

```bash
php composer.phar require --dev wiremock-php/wiremock-php:1.39
```

Usage
-----
### API
The API is based directly on WireMock's Java API, so see the [WireMock documentation](http://wiremock.org/) for general
help with interacting with WireMock.

### Differences to Java API
Unfortunately, PHP doesn't support anything like Java's static import of methods, so the result is slightly less pretty
than the Java API. Most methods are static on the WireMock class, but methods which are static in Java are instance
methods in PHP. Those methods are:

- stubFor
- verify
- findAll
- reset, resetToDefault, resetAllScenarios
- setGlobalFixedDelay, addRequestProcessingDelay
- isAlive (not part of the WireMock API, used to check if the standalone service is up and running)

Also, Java has an overload of withBody() that takes a byte array. Byte arrays are less common in PHP, so instead,
`withBodyData()` is provided, which takes a string to base64 encoded. To produce an appropriate string from an array
of bytes, use [pack](http://php.net/pack).

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


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/rowanhill/wiremock-php/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
