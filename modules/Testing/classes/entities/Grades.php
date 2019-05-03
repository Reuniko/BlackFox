<?php

namespace Testing;

class Grades extends \System\SCRUD {

	public function Init() {
		$this->name = 'Grades';
		$this->structure = [
			'ID'         => self::ID,
			'TITLE'      => [
				'TYPE'  => 'STRING',
				'NAME'  => 'Title',
				'VITAL' => true,
			],
			'CAPTAIN'    => [
				'NAME' => 'Captain',
				'TYPE' => 'OUTER',
				'LINK' => 'Students',
			],
			'STUDENTS'   => [
				'NAME'  => 'Students',
				'TYPE'  => 'INNER',
				'LINK'  => 'Students',
				'FIELD' => 'GRADE',
			],
			'TIMETABLES' => [
				'NAME'  => 'Timetable',
				'TYPE'  => 'INNER',
				'LINK'  => 'Timetable',
				'FIELD' => 'GRADE',
			],
		];
	}

	public function Fill() {
		foreach (['A', 'B', 'C'] as $class_letter) {
			foreach ([1, 2, 3, 4, 5, 7, 8, 9, 10, 11] as $class_number) {
				$this->Create([
					'TITLE'   => $class_number . $class_letter,
					'CAPTAIN' => random_int(1, 9),
				]);
			}
		}
	}
}