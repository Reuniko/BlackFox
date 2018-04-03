<?php
/** @var \System\Unit $this */
return [
	'Admin' => [
		'NAME'     => 'Админка',
		'LINK'     => '/admin/Admin/',
		'CHILDREN' => [
			'Admin_TableSettings' => [
				'NAME' => 'Настройки таблиц',
				'LINK' => '/admin/Admin/TableSettings.php',
			],
			'Admin_PHPConsole' => [
				'NAME' => 'PHP консоль',
				'LINK' => '/admin/Admin/PHPConsole.php',
			],
		],
	],
];
