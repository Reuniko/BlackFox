<?php
// Script for upgrade from console

if (php_sapi_name() <> 'cli') die('Console usage only');
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../';

require_once("includes.php");
\BlackFox\Engine::I()->Upgrade();