<?php
/** @var \System\Unit $this */
return [
	'System' => [
		'NAME'     => 'Система',
		'LINK'     => '/admin/System/',
		'EXPANDER' => true,
		'CHILDREN' => [
			'panel'               => [
				'NAME'     => 'Панель управления',
				'LINK'     => '/admin/System/Panel.php',
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
				'NAME' => 'Контентные страницы',
				'LINK' => '/admin/System/Content.php',
			],
			'System_Redirects'    => [
				'NAME' => 'Редиректы',
				'LINK' => '/admin/System/Redirects.php',
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
			'System_Log'          => [
				'NAME' => 'Журнал системы',
				'LINK' => '/admin/System/Log.php',
			],
		],
	],
];