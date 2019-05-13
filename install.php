<?php
// Script for initial install from console

if (php_sapi_name() <> 'cli') die('Console usage only');
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../';

require_once("modules/system/classes/patterns/Instanceable.php");
require_once("modules/system/classes/Engine.php");
require_once("modules/system/T.php");
require_once("modules/system/debug.php");

\System\Engine::I()->RegisterModuleClasses('System');
\System\Module::I()->Upgrade();
\System\Modules::I()->Delete(['ID' => 'System']);
\System\Modules::I()->Create(['ID' => 'System']);

\System\Engine::I()->RegisterModuleClasses('Admin');
\Admin\Module::I()->Upgrade();
\System\Modules::I()->Delete(['ID' => 'Admin']);
\System\Modules::I()->Create(['ID' => 'Admin']);

$group_id = \System\Groups::I()->Create([
	'CODE' => 'root',
	'NAME' => 'Root',
]);
$user_id = \System\Users::I()->Create([
	'LOGIN'    => 'Root',
	'PASSWORD' => '123456',
]);
\System\Users2Groups::I()->Create([
	'USER'  => $user_id,
	'GROUP' => $group_id,
]);