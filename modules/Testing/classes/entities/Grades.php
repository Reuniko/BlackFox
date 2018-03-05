<?php

namespace Testing;

class Grades extends \System\SCRUD {

	public function Init() {
		$this->name = 'Классы';
		$this->structure = [
			'ID'       => self::ID,
			'TITLE'    => [
				'TYPE'  => 'STRING',
				'NAME'  => 'Title',
				'VITAL' => true,
				'SHOW'  => true,
			],
			'STUDENTS' => [
				'NAME'  => 'Students',
				'TYPE'  => 'INNER',
				'LINK'  => 'Testing\Students',
				'FIELD' => 'GRADE',
			],
			'TIMETABLES' => [
				'NAME'  => 'Timetables',
				'TYPE'  => 'INNER',
				'LINK'  => 'Testing\Timetable',
				'FIELD' => 'GRADE',
			],
		];
	}

	public function Fill() {
		foreach (['A', 'B', 'C'] as $class_letter) {
			foreach ([1, 2, 3, 4, 5, 7, 8, 9, 10, 11] as $class_number) {
				$this->Create(['TITLE' => $class_number . $class_letter]);
			}
		}
	}
}