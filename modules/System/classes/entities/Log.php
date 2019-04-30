<?php

namespace System;

class Log extends SCRUD {
	public function Init() {
		$this->name = T([
			'en' => 'System log',
			'ru' => 'Журнал системы',
		]);
		$this->structure = [
			'ID'      => self::ID,
			'MOMENT'  => [
				'TYPE'     => 'DATETIME',
				'NAME'     => T([
					'en' => 'Moment',
					'ru' => 'Момент',
				]),
				'NOT_NULL' => true,
			],
			'IP'      => [
				'TYPE' => 'STRING',
				'NAME' => T([
					'en' => 'IP address',
					'ru' => 'IP адрес',
				]),
			],
			'USER'    => [
				'TYPE'  => 'OUTER',
				'NAME'  => T([
					'en' => 'User',
					'ru' => 'Пользователь',
				]),
				'INDEX' => true,
				'LINK'  => 'Users',
			],
			'TYPE'    => [
				'TYPE'        => 'STRING',
				'NAME'        => T([
					'en' => 'Event type',
					'ru' => 'Тип события',
				]),
				'DESCRIPTION' => T([
					'en' => 'Symbolic code',
					'ru' => 'Символьный код',
				]),
				'NOT_NULL'    => true,
				'INDEX'       => true,
			],
			'MESSAGE' => [
				'TYPE' => 'TEXT',
				'NAME' => T([
					'en' => 'Message',
					'ru' => 'Сообщение',
				]),
			],
			'DATA'    => [
				'TYPE' => 'ARRAY',
				'NAME' => T([
					'en' => 'Additional data',
					'ru' => 'Дополнительные данные',
				]),
			],
		];
	}

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
