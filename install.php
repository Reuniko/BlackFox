<?php
// Script for initial install from console

if (php_sapi_name() <> 'cli') die('Console usage only');
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../';

require_once("includes.php");

\System\Engine::I()->RegisterModuleClasses('System');
\System\Module::I()->Upgrade();
echo "\r\n System module upgraded;";

\System\Engine::I()->RegisterModuleClasses('Admin');
\Admin\Module::I()->Upgrade();
echo "\r\n Admin module upgraded;";

try {
	$group_id = \System\Groups::I()->Create([
		'CODE' => 'root',
		'NAME' => 'Root',
	]);
	echo "\r\n Group 'root' created;";
	$user_id = \System\Users::I()->Create([
		'LOGIN'    => 'Root',
		'PASSWORD' => '123456',
	]);
	echo "\r\n User 'Root' created;";
	\System\Users2Groups::I()->Create([
		'USER'  => $user_id,
		'GROUP' => $group_id,
	]);
	echo "\r\n User linked to group;";
} catch (\System\Exception $error) {
	echo "\r\n Error:" . $error->GetMessage();
}