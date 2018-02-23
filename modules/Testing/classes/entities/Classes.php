<?php

namespace Testing;

class Classes extends \System\SCRUD {

	public function Init() {
		$this->name = 'Классы ';
		$this->structure = [
			'ID'       => self::ID,
			'TITLE'    => [
				'TYPE'  => 'STRING',
				'NAME'  => 'String',
				'VITAL' => true,
				'SHOW'  => true,
			],
			'STUDENTS' => [
				'NAME'  => 'Students',
				'TYPE'  => 'INNER',
				'LINK'  => 'Testing\Students',
				'FIELD' => 'CLASS',
			],
		];
	}
}