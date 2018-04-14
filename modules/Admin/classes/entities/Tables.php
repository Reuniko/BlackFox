<?php

namespace Admin;

class Tables extends \System\SCRUD {

	public function Init() {
		$this->name = 'Таблицы';
		$this->structure = [
			'ENTITY'    => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Сущность',
				'NOT_NULL' => true,
				'INDEX'    => true,
				'PRIMARY'  => true,
				'VITAL'    => true,
			],
			'NAME'      => [
				'TYPE' => 'STRING',
				'NAME' => 'Имя',
			],
			'GROUPS'    => [
				'TYPE' => 'ARRAY',
				'NAME' => 'Группы',
			],
			'STRUCTURE' => [
				'TYPE' => 'ARRAY',
				'NAME' => 'Структура',
			],
		];
	}

}

