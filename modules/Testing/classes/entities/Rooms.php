<?php

namespace Testing;

class Rooms extends \System\SCRUD {

	public function Init() {
		$this->name = 'Аудитории';
		$this->structure = [
			'ID'    => self::ID,
			'TITLE' => [
				'TYPE'     => 'STRING',
				'NAME'     => 'A. Number',
				'NOT_NULL' => true,
				'VITAL'    => true,
				'SHOW'     => true,
			],
		];
	}

	public function Fill() {
		$rooms = [101, 102, 103, 104, 105, 106, 107, 201, 203, 205, 207, 209, 301, 304, 307, 311];
		foreach ($rooms as $room) {
			$this->Create(['TITLE' => 'R-' . $room]);
		}
	}

}

