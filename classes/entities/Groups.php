<?php

namespace BlackFox;

class Groups extends SCRUD {

	public function Init() {
		$this->name = T([
			'en' => 'User groups',
			'ru' => 'Группы пользователей',
		]);
		$this->fields += [
			'ID'          => self::ID,
			'CODE'        => [
				'TYPE'     => 'STRING',
				'NAME'     => T([
					'en' => 'Symbolic code',
					'ru' => 'Символьный код',
				]),
				'NOT_NULL' => true,
				'INDEX'    => true,
				'VITAL'    => true,
				'UNIQUE'   => true,
			],
			'NAME'        => [
				'TYPE'     => 'STRING',
				'NAME'     => T([
					'en' => 'Name',
					'ru' => 'Имя',
				]),
				'NOT_NULL' => true,
				'INDEX'    => true,
				'VITAL'    => true,
			],
			'DESCRIPTION' => [
				'TYPE' => 'TEXT',
				'NAME' => T([
					'en' => 'Description',
					'ru' => 'Описание',
				]),
			],
		];
	}

	public function GetElementTitle(array $element) {
		return "{$element['NAME']} ({$element['CODE']})";
	}
}
