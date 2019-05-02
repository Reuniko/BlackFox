<?php
/** @var \System\Unit $this */
return [
	'Admin' => [
		'NAME'     => T([
			'en' => 'Admin',
			'ru' => 'Админка',
		]),
		'LINK'     => '/admin/Admin/',
		'EXPANDER' => true,
		'CHILDREN' => [
			'Admin_TableSettings' => [
				'NAME' => T([
					'en' => 'Table settings',
					'ru' => 'Настройки таблиц',
				]),
				'LINK' => '/admin/Admin/TableSettings.php',
			],
			'Admin_PHPConsole'    => [
				'NAME' => T([
					'en' => 'PHP console',
					'ru' => 'PHP консоль',
				]),
				'LINK' => '/admin/Admin/PHPConsole.php',
			],
		],
	],
];
