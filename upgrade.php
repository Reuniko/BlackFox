<?php
// Script for upgrade from console

if (php_sapi_name() <> 'cli') die('Console usage only');
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../';

require_once("includes.php");
$time1 = microtime(true);
\BlackFox\Engine::I()->Upgrade();
$time2 = microtime(true);
echo $time2 - $time1;