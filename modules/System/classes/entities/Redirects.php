<?php

namespace System;

class Redirects extends \System\SCRUD {

	public function Init() {
		$this->name = 'Редиректы';
		$this->structure = [
			'ID'       => self::ID,
			'URL'      => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Адрес',
				'NOT_NULL' => true,
				'INDEX'    => true,
				'UNIQUE'   => true,
			],
			'REDIRECT' => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Редирект',
				'NOT_NULL' => true,
			],
			'COUNT'    => [
				'TYPE'     => 'NUMBER',
				'NAME'     => 'Количество переходов',
				'DEFAULT'  => 0,
				'NOT_NULL' => true,
				'DISABLED' => true,
			],
			'NOTES'    => [
				'TYPE' => 'TEXT',
				'NAME' => 'Заметки',
			],
		];
	}

}
