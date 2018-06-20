<?php
namespace System;

class Log extends SCRUD {
	public $name = 'Журнал системы';
	public $structure = [
		'ID'      => self::ID,
		'MOMENT'  => [
			'TYPE'     => 'DATETIME',
			'NAME'     => 'Момент',
			'NOT_NULL' => true,
		],
		'IP'      => [
			'TYPE' => 'STRING',
			'NAME' => 'IP адрес',
		],
		'USER'    => [
			'TYPE'  => 'OUTER',
			'NAME'  => 'Пользователь',
			'INDEX' => true,
			'LINK'  => 'Users',
		],
		'TYPE'    => [
			'TYPE'        => 'STRING',
			'NAME'        => 'Тип события',
			'DESCRIPTION' => 'Символьный код',
			'NOT_NULL'    => true,
			'INDEX'       => true,
		],
		'MESSAGE' => [
			'TYPE' => 'TEXT',
			'NAME' => 'Сообщение',
		],
		'DATA'    => [
			'TYPE' => 'ARRAY',
			'NAME' => 'Дополнительные данные',
		],
	];

	public function Create($fields) {
		$fields['MOMENT'] = time();
		$fields['USER'] = $fields['USER'] ?: User::I()->ID ?: null;
		$fields['IP'] = $_SERVER['REMOTE_ADDR'];
		return parent::Create($fields);
	}

	public function Update($filter = [], $fields = []) {
		throw new ExceptionNotAllowed();
	}

	public function Delete($filter = []) {
		throw new ExceptionNotAllowed();
	}
}
