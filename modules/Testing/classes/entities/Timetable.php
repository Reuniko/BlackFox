<?php

namespace Testing;

class Timetable extends \System\SCRUD {
	public function Init() {
		$this->name = 'Расписание занятий';
		$this->structure = [
			'ID'       => self::ID,
			'GRADE'    => [
				'TYPE'     => 'OUTER',
				'NAME'     => 'Класс',
				'NOT_NULL' => true,
			],
			'ROOM'     => [
				'TYPE'     => 'OUTER',
				'NAME'     => 'Аудитория',
				'NOT_NULL' => true,
			],
			'START'    => [
				'TYPE'     => 'DATETIME',
				'NAME'     => 'Время начала занятий',
				'NOT_NULL' => true,
			],
			'DURATION' => [
				'TYPE'     => 'NUMBER',
				'NAME'     => 'Продолжительность (в часах)',
				'NOT_NULL' => true,
				'DEFAULT'  => 1,
			],
		];
	}

	public function Fill() {
		for ($i = 0; $i < 1000; $i++) {
			$this->Create([
				'GRADE' => Grades::I()->Pick([], ['RAND()' => 'ASC']),
				'ROOM'  => Rooms::I()->Pick([], ['RAND()' => 'ASC']),
				'START' => time() + $i * 3600,
			]);
		}
	}
}

