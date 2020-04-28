<?php

namespace BlackFox;

class Redirects extends \BlackFox\SCRUD {

	public function Init() {
		$this->name = T([
			'en' => 'Redirects',
			'ru' => 'Редиректы',
		]);
		$this->fields = [
			'ID'       => self::ID,
			'URL'      => [
				'TYPE'     => 'STRING',
				'NAME'     => T([
					'en' => 'URL',
					'ru' => 'Адрес',
				]),
				'NOT_NULL' => true,
				'INDEX'    => true,
				'UNIQUE'   => true,
			],
			'REDIRECT' => [
				'TYPE'     => 'STRING',
				'NAME'     => T([
					'en' => 'Redirect',
					'ru' => 'Редирект',
				]),
				'NOT_NULL' => true,
			],
			'COUNT'    => [
				'TYPE'     => 'INTEGER',
				'NAME'     => T([
					'en' => 'Click counter',
					'ru' => 'Количество переходов',
				]),
				'DEFAULT'  => 0,
				'NOT_NULL' => true,
				'DISABLED' => true,
			],
			'NOTES'    => [
				'TYPE' => 'TEXT',
				'NAME' => T([
					'en' => 'Notes',
					'ru' => 'Заметки',
				]),
			],
		];
	}

}
