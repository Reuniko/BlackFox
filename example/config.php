<?php
/**
 * This is an example of /config.php
 *
 * Tasks:
 * - replace word 'Site' with the unique name of your project's folder\namespace
 * - configure key 'database'
 * - delete this comment
 */
return [
	'debug'     => true,
	'cores'     => [
		'Site'     => $_SERVER['DOCUMENT_ROOT'] . '/Site',
		'BlackFox' => $_SERVER['DOCUMENT_ROOT'] . '/BlackFox',
	],
	'roots'     => [
		'/Site/root'     => $_SERVER['DOCUMENT_ROOT'] . '/Site/root',
		'/BlackFox/root' => $_SERVER['DOCUMENT_ROOT'] . '/BlackFox/root',
	],
	'templates' => [
		'main'  => '/Site/templates/main',
		'admin' => '/BlackFox/templates/admin',
	],
	'languages' => [
		//'en' => 'English',
		//'ru' => 'Русский',
	],
	'overrides' => [
		// 'BlackFox\Engine' => 'Site\Engine',
		// 'BlackFox\Cache' => 'BlackFox\CacheRedis',
		'BlackFox\Database' => 'BlackFox\MySQL',
	],
	'database'  => [
		'HOST'     => 'localhost',
		'PORT'     => 3306,
		'USER'     => '',
		'PASSWORD' => '',
		'DATABASE' => '',
	],
];