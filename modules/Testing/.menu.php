<?php
/** @var \System\Unit $this */
return [
	'Testing' => [
		'NAME'     => T([
			'en' => 'Tests',
			'ru' => 'Тесты',
		]),
		'LINK'     => '/admin/Testing',
		'EXPANDER' => true,
		'CHILDREN' => [
			'Tables' => [
				'NAME'     => T([
					'en' => 'Tables',
					'ru' => 'Таблицы',
				]),
				'CHILDREN' => [
					'Table1'    => [
						'NAME' => 'Table1',
						'LINK' => '/admin/Testing/Table1.php',
					],
					'Grades'    => [
						'NAME' => 'Grades',
						'LINK' => '/admin/Testing/Grades.php',
					],
					'Rooms'     => [
						'NAME' => 'Rooms',
						'LINK' => '/admin/Testing/Rooms.php',
					],
					'Students'  => [
						'NAME' => 'Students',
						'LINK' => '/admin/Testing/Students.php',
					],
					'Timetable' => [
						'NAME' => 'Timetable',
						'LINK' => '/admin/Testing/Timetable.php',
					],
				],
			],
			'SCRUD'  => [
				'NAME' => T([
					'en' => 'Testing of SCRUD',
					'ru' => 'Тестирование SCRUD',
				]),
				'LINK' => '/admin/Testing/SCRUD.php',
			],
			'CACHE'  => [
				'NAME' => T([
					'en' => 'Testing of Cache',
					'ru' => 'Тестирование кеша',
				]),
				'LINK' => '/admin/Testing/Cache.php',
			],
		],
	],
];