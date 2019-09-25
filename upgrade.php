<?php
// Script for upgrade from console

if (php_sapi_name() <> 'cli') die('Console usage only');
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../';

try {
	require_once("includes.php");
	\System\Engine::I()->UpgradeActiveModules();
} catch (\System\ExceptionSQL $ExceptionSQL) {
	echo $ExceptionSQL->GetMessage() . ' ' . $ExceptionSQL->SQL;
}
