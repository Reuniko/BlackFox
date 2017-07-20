<?php
return [
	'debug'    => true,
	'root'     => $_SERVER['DOCUMENT_ROOT'] . '/root',
	'cores'    => [
		$_SERVER['DOCUMENT_ROOT'] . '/site',
		$_SERVER['DOCUMENT_ROOT'] . '/core',
	],
	'database' => [
		'HOST'     => 'localhost',
		'PORT'     => '3306',
		'USER'     => 'root',
		'PASSWORD' => '',
		'DATABASE' => 'tigris',
	],
	'template' => 'bootstrap',
];