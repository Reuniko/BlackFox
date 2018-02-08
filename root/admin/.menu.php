<?php
return [
	'SYSTEM' => [
		'NAME'     => 'Система',
		'CHILDREN' => [
			'root'                => [
				'NAME' => 'Рабочий стол',
				'LINK' => '/admin/index.php',
			],
			'~upgrade'            => [
				'NAME' => '~upgrade',
				'LINK' => '/admin/_upgrade.php',
			],
			'Test0'               => [
				'NAME'     => 'Test0',
				'LINK'     => '/admin/Test0/',
				'CHILDREN' => [
					'Test1' => [
						'NAME' => '/admin/Test1/',
						'LINK' => '/admin/Test1/',
					],
					'Test2' => [
						'NAME' => '/admin/Test2/',
						'LINK' => '/admin/Test2/',
					],
					'Test3' => [
						'NAME'     => '/admin/Test3/',
						'LINK'     => '/admin/Test3/',
						'CHILDREN' => [
							'Test4' => [
								'NAME' => '/admin/Test4/',
								'LINK' => '/admin/Test4/',
							],
							'Test5' => [
								'NAME' => '/admin/Test5/',
								'LINK' => '/admin/Test5/',
							],
						],
					],
				],
			],
			'System_Modules'      => [
				'NAME' => 'Модули',
				'LINK' => '/admin/System/Modules.php',
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
		]
	],
];
