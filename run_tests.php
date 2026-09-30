<?php
require __DIR__ . '/vendor/autoload.php';

$cmd = '"' . PHP_BINARY . '" vendor/phpunit/phpunit/phpunit';
passthru($cmd, $exitCode);
exit($exitCode);
