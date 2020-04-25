<?php
global $CONFIG;
$CONFIG = [
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
	'database'  => [
		'HOST'     => 'localhost',
		'PORT'     => 3306,
		'USER'     => '',
		'PASSWORD' => '',
		'DATABASE' => '',
	],
	'redirects' => [
		// 'BlackFox\\Engine' => '<your namespace>\\Engine',
		'BlackFox\Database' => 'BlackFox\DatabaseDriverMySQL',
	],
];
return $CONFIG;