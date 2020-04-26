<?php
// Script for initial install from console

if (php_sapi_name() <> 'cli') die('Console usage only');
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../';

require_once("includes.php");
\BlackFox\Engine::I()->Upgrade();
echo "\r\n BlackFox core has been upgraded;";

try {
	$group_id = \BlackFox\Groups::I()->Create([
		'CODE' => 'root',
		'NAME' => 'Root',
	]);
	echo "\r\n Group 'root' created;";
	$user_id = \BlackFox\Users::I()->Create([
		'LOGIN'    => 'Root',
		'PASSWORD' => '123456',
	]);
	echo "\r\n User 'Root' created;";
	\BlackFox\Users2Groups::I()->Create([
		'USER'  => $user_id,
		'GROUP' => $group_id,
	]);
	echo "\r\n User linked to group;";
} catch (\BlackFox\Exception $error) {
	echo "\r\n Error: " . $error->GetMessage();
}