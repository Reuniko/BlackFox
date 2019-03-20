<?php

namespace Testing;

class Students extends \System\SCRUD {

	public function Init() {
		$this->name = 'Студенты';
		$this->structure = [
			'ID'         => self::ID,
			'FIRST_NAME' => [
				'TYPE'  => 'STRING',
				'NAME'  => 'First name',
				'VITAL' => true,
			],
			'LAST_NAME'  => [
				'TYPE' => 'STRING',
				'NAME' => 'Last name',
			],
			'GRADE'      => [
				'TYPE'    => 'OUTER',
				'LINK'    => 'Testing\Grades',
				'NAME'    => 'Grade',
				'FOREIGN' => 'RESTRICT',
			],
		];
	}

	public function GetElementTitle($element = []) {
		return $element['FIRST_NAME'];
	}

	public function Fill() {
		$names = file(__DIR__ . '/data/names.txt', FILE_IGNORE_NEW_LINES);
		$lasts = ['J', 'G', 'V', 'X', 'Z'];
		for ($i = 0; $i < 800; $i++) {
			$this->Create([
				'FIRST_NAME' => $names[array_rand($names)],
				'LAST_NAME'  => $lasts[array_rand($lasts)] . '.',
				'GRADE'      => Grades::I()->Pick([], null, ['{RANDOM}' => 'ASC']),
			]);
		}
	}
}