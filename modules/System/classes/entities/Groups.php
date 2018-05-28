<?php

namespace System;

class Groups extends SCRUD {

	public function Init() {
		$this->name = 'Группы пользователей';
		$this->structure += [
			'ID'          => self::ID,
			'CODE'        => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Символьный код',
				'NOT_NULL' => true,
				'INDEX'    => true,
				'VITAL'    => true,
			],
			'NAME'        => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Имя',
				'NOT_NULL' => true,
				'INDEX'    => true,
				'VITAL'    => true,
			],
			'DESCRIPTION' => [
				'TYPE' => 'TEXT',
				'NAME' => 'Описание',
			],
		];
	}

	public function GetElementTitle($element = []) {
		return "{$element['NAME']} ({$element['CODE']})";
	}
}
