<?php
namespace System;
class Modules extends SCRUD {

	final public function Init() {
		$this->name = 'Модули';
		$this->composition = [
			'SYSTEM' => [
				'NAME'   => 'Системные поля',
				'FIELDS' => [
					'ID'          => [
						'TYPE'           => 'STRING',
						'NAME'           => 'ID',
						'INDEX'          => true,
						'PRIMARY'        => true,
						'NOT_NULL'       => true,
						'AUTO_INCREMENT' => false,
						'DISABLED'       => false,
						'JOIN'           => true,
					],
					'NAME'        => [
						'TYPE' => 'STRING',
						'NAME' => 'Имя',
					],
					'DESCRIPTION' => [
						'TYPE' => 'STRING',
						'NAME' => 'Описание',
					],
					'VERSION'     => [
						'TYPE' => 'STRING',
						'NAME' => 'Версия',
					],
				],
			],
		];
	}

}