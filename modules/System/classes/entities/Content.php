<?php

namespace System;

class Content extends \System\SCRUD {

	public function Init() {
		$this->name = 'Контентные страницы';
		$this->structure = [
			'ID'          => self::ID,
			'URL'         => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Адрес',
				'NOT_NULL' => true,
				'INDEX'    => true,
				'UNIQUE'   => true,
			],
			'REDIRECT'    => [
				'TYPE' => 'STRING',
				'NAME' => 'Редирект',
			],
			'TITLE'       => [
				'TYPE' => 'STRING',
				'NAME' => 'Заголовок',
			],
			'DESCRIPTION' => [
				'TYPE' => 'STRING',
				'NAME' => 'Описание',
			],
			'KEYWORDS'    => [
				'TYPE' => 'STRING',
				'NAME' => 'Ключевые слова',
			],
			'CONTENT'     => [
				'TYPE'    => 'TEXT',
				'NAME'    => 'Содержимое',
				'WYSIWYG' => true,
			],
		];
	}

}
