<?php
/** @var \System\Component $this */
return [
	'Admin' => [
		'NAME'     => 'Админка',
		'LINK'     => '/admin/Admin/',
		'CHILDREN' => [
			'Admin_TableSettings' => [
				'NAME' => 'Настройки',
				'LINK' => '/admin/Admin/TableSettings.php',
			],
		],
	],
];
