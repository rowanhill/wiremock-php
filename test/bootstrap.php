<?php
require_once('../vendor/autoload.php');
require_once './WireMock/HamcrestTestCase.php';
require_once './WireMock/Integration/WireMockIntegrationTest.php';
require_once './WireMock/Integration/MappingsAssertionFunctions.php';
require_once './WireMock/Integration/TestClient.php';
\Hamcrest\Util::registerGlobalFunctions();
\Phake::setClient(Phake::CLIENT_PHPUNIT8);

// Some systems (e.g. OSX) don't block on commands exec()ed in the below fashion. Other systems (e.g. Travis) do.
// Systems which do block need to have their output redirected from stdout/stderr if the command being exec()ed is
// long running.
// Also, some systems (e.g. OSX) fail the tests if the output of starting WireMock is redirected (possibly because
// they're running tests in parallel?), whereas others (e.g. Travis).
// So far, there's a 1-to-1 mapping between these kinds of systems, but it's unclear if that's just chance or not.
// For now, just check which kind of system we're on, so we know whether to redirect output or not.
$startTime = microtime(true);
exec('(./exec_test.sh) 2>&1 &');
$endTime = microtime(true);
$sleepTime = (($endTime - $startTime) * 1000);
echo "Took $sleepTime millis to sleep in background exec()\n";
define('EXEC_BLOCKS_ON_OUTPUT', $sleepTime > 5000);

// Report all errors - being as strict as possible ensures the library will be compatible with the most environments
error_reporting(E_ALL);
