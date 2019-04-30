<?php

namespace System;

class Modules extends SCRUD {

	final public function Init() {
		$this->name = T([
			'en' => 'Modules',
			'ru' => 'Модули',
		]);
		$this->structure += [
			'ID'          => [
				'TYPE'           => 'STRING',
				'NAME'           => 'ID',
				'INDEX'          => true,
				'PRIMARY'        => true,
				'NOT_NULL'       => true,
				'AUTO_INCREMENT' => false,
				'DISABLED'       => false,
				'VITAL'          => true,
			],
			'NAME'        => [
				'TYPE' => 'STRING',
				'NAME' => T([
					'en' => 'Name',
					'ru' => 'Имя',
				]),
			],
			'DESCRIPTION' => [
				'TYPE' => 'STRING',
				'NAME' => T([
					'en' => 'Description',
					'ru' => 'Описание',
				]),
			],
			'VERSION'     => [
				'TYPE' => 'STRING',
				'NAME' => T([
					'en' => 'Version',
					'ru' => 'Версия',
				]),
			],
			'SORT'        => [
				'TYPE' => 'NUMBER',
				'NAME' => T([
					'en' => 'Sort',
					'ru' => 'Сортировка',
				]),
			],
		];
	}

}