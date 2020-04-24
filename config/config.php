<?php
global $CONFIG;
$CONFIG = [
	'debug'     => true,
	'cores'     => [
		'/site' => $_SERVER['DOCUMENT_ROOT'] . '/site',
		'/core' => $_SERVER['DOCUMENT_ROOT'] . '/core',
	],
	'roots'     => [
		'/site/root' => $_SERVER['DOCUMENT_ROOT'] . '/site/root',
		'/core/root' => $_SERVER['DOCUMENT_ROOT'] . '/core/root',
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
	'template'  => 'bootstrap',
	'redirects' => [
		// 'BlackFox\\Engine' => '<your namespace>\\Engine',
		'BlackFox\Database' => 'BlackFox\DatabaseDriverMySQL',
	],
];
return $CONFIG;