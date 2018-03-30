<?php
/** @var \System\Unit $this */
return [
	'Testing' => [
		'NAME'     => 'Тесты',
		'LINK'     => '/admin/Testing',
		'CHILDREN' => [
			'Tables' => [
				'NAME'     => 'Таблицы',
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
				'NAME' => 'Тестирование SCRUD',
				'LINK' => '/admin/Testing/SCRUD.php',
			],
		],
	],
];