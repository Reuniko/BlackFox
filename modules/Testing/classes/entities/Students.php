<?php

namespace Testing;

class Students extends \System\SCRUD {

	public function Init() {
		$this->name = 'Студенты';
		$this->structure = [
			'ID'         => self::ID,
			'FIRST_NAME' => [
				'TYPE'  => 'STRING',
				'NAME'  => 'String',
				'VITAL' => true,
				'SHOW'  => true,
			],
			'LAST_NAME'  => [
				'TYPE'  => 'STRING',
				'NAME'  => 'String',
				'VITAL' => true,
				'SHOW'  => true,
			],
			'CLASS'      => [
				'TYPE' => 'OUTER',
				'LINK' => 'Testing\Classes',
				'NAME' => 'Class',
			],
		];
	}
}