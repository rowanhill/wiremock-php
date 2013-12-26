<?php
require_once('../vendor/autoload.php');
require_once('../vendor/hafriedlander/phockito/Phockito_Globals.php');
Phockito::include_hamcrest(true);

$startTime = microtime(true);
exec('(./exec_test.sh) 2>&1 &');
$endTime = microtime(true);
$sleepTime = (($endTime-$startTime) * 1000);
echo "Took $sleepTime millis to sleep in background exec()\n";
define('EXEC_BLOCKS_ON_OUTPUT', $sleepTime > 5000);