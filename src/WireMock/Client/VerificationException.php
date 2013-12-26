<?php

namespace WireMock\Client;

//
// If PHPUnit is on the include path, VerificationException extends AssertionFailedError (to automatically fail tests).
// If not, it just extends Exception (to prevent forcing a dependency on PHPUnit).
//
if (class_exists('PHPUnit_Framework_AssertionFailedError')) {

    class VerificationException extends \PHPUnit_Framework_AssertionFailedError
    {

    }

} else {

    class VerificationException extends \Exception
    {

    }

}