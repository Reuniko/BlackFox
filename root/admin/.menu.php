<?php
return [
	'System'  => [
		'NAME'     => 'Система',
		'LINK'     => '/admin/System/',
		'CHILDREN' => [
			'panel'               => [
				'NAME'     => 'Панель управления',
				'LINK'     => '/admin/',
				'CHILDREN' => [
					'~upgrade' => [
						'NAME' => '~upgrade',
						'LINK' => '/admin/_upgrade.php',
					],
				],
			],
			'System_Modules'      => [
				'NAME' => 'Модули',
				'LINK' => '/admin/System/Modules.php',
			],
			'System_Content'      => [
				'NAME' => 'Контент',
				'LINK' => '/admin/System/Content.php',
			],
			'System_Users'        => [
				'NAME' => 'Пользователи',
				'LINK' => '/admin/System/Users.php',
			],
			'System_Groups'       => [
				'NAME' => 'Группы',
				'LINK' => '/admin/System/Groups.php',
			],
			'System_Users2Groups' => [
				'NAME' => 'Пользователи в группах',
				'LINK' => '/admin/System/Users2Groups.php',
			],
			'System_Files'        => [
				'NAME' => 'Файлы',
				'LINK' => '/admin/System/Files.php',
			],
			'Admin_TableSettings' => [
				'NAME' => 'Настройки',
				'LINK' => '/admin/Admin/TableSettings.php',
			],
		],
	],
	'Testing' => [
		'NAME'     => 'Тесты',
		'LINK'     => '/admin/Testing',
		'CHILDREN' => [
			'TestScrudTableSimple' => [
				'NAME' => 'Таблица для тестирования №1',
				'LINK' => '/admin/Testing/Table1.php',
			],
			'SCRUD'                => [
				'NAME' => 'Тестирование SCRUD',
				'LINK' => '/admin/Testing/SCRUD.php',
			],
		],
	],
];
