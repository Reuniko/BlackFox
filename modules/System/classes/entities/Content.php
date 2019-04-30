<?php

namespace System;

class Content extends \System\SCRUD {

	public function Init() {
		$this->name = T([
			'en' => 'Content pages',
			'ru' => 'Контентные страницы',
		]);
		$this->structure = [
			'ID'          => self::ID,
			'URL'         => [
				'TYPE'        => 'STRING',
				'NAME'        => T([
					'en' => 'Url',
					'ru' => 'Адрес',
				]),
				'NOT_NULL'    => true,
				'INDEX'       => true,
				'UNIQUE'      => true,
				'DESCRIPTION' => T([
					'en' => 'Relative to the site root',
					'ru' => 'Относительно корня сайта',
				]),
			],
			'TITLE'       => [
				'TYPE' => 'STRING',
				'NAME' => T([
					'en' => 'Title',
					'ru' => 'Заголовок',
				]),
			],
			'DESCRIPTION' => [
				'TYPE' => 'STRING',
				'NAME' => T([
					'en' => 'Description',
					'ru' => 'Описание',
				]),
			],
			'KEYWORDS'    => [
				'TYPE' => 'STRING',
				'NAME' => T([
					'en' => 'Key words',
					'ru' => 'Ключевые слова',
				]),
			],
			'CONTENT'     => [
				'TYPE'    => 'TEXT',
				'NAME'    => T([
					'en' => 'Content',
					'ru' => 'Содержимое',
				]),
				'WYSIWYG' => true,
			],
		];
	}

}
