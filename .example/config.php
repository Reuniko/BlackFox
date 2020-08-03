<?php
/**
 * This is an example of /config.php
 *
 * Tasks:
 * - replace word 'Site' with the unique name of your project's folder\namespace
 * - select database engine
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
		// pick one:
		// 'BlackFox\Database' => 'BlackFox\MySQL',
		// 'BlackFox\Database' => 'BlackFox\Postgres',

		// pick none or one:
		// 'BlackFox\Captcha' => 'BlackFox\KCaptcha',
		// 'BlackFox\Captcha' => 'BlackFox\CaptchaGoogleRecaptchaV2',

		// pick none or one:
		// 'BlackFox\Cache' => 'BlackFox\CacheRedis',
	],
	'database'  => [
		'HOST'     => 'localhost',
		'PORT'     => 3306,
		'USER'     => '',
		'PASSWORD' => '',
		'DATABASE' => '',
	],
];