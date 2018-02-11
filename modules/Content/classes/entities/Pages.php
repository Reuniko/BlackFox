<?php

namespace Content;

class Pages extends \System\SCRUD {

	public function Init() {
		$this->name = 'Контентные страницы';
		$this->structure = [
			'ID'      => self::ID,
			'URL'     => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Адрес страницы',
				'NOT_NULL' => true,
				'INDEX'    => true,
			],
			'CONTENT' => [
				'TYPE'    => 'TEXT',
				'NAME'    => 'Содержимое страницы',
				'WYSIWYG' => true,
			],
		];
	}

}
