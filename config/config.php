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
	'database'  => [
		'HOST'     => 'localhost',
		'PORT'     => '3306',
		'USER'     => 'root',
		'PASSWORD' => '',
		'DATABASE' => 'tigris',
	],
	'template'  => 'bootstrap',
	'redirects' => [
		// 'System\\Engine' => '<your namespace>\\Engine',
	],
];
return $CONFIG;