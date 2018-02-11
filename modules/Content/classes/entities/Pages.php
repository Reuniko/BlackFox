<?php

namespace Content;

class Pages extends \System\SCRUD {

	public function Init() {
		$this->name = 'Контентные страницы';
		$this->structure = [
			'ID'      => self::ID,
			'URL'     => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Адрес',
				'NOT_NULL' => true,
				'INDEX'    => true,
			],
			'TITLE'   => [
				'TYPE' => 'STRING',
				'NAME' => 'Заголовок',
			],
			'CONTENT' => [
				'TYPE'    => 'TEXT',
				'NAME'    => 'Содержимое',
				'WYSIWYG' => true,
			],
		];
	}

}
