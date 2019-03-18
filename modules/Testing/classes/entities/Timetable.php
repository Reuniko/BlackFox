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
				'LINK'     => 'Testing\Grades',
				'NOT_NULL' => true,
			],
			'ROOM'     => [
				'TYPE'     => 'OUTER',
				'NAME'     => 'Аудитория',
				'LINK'     => 'Testing\Rooms',
				'NOT_NULL' => true,
			],
			'START'    => [
				'TYPE'     => 'DATETIME',
				'NAME'     => 'Время начала занятий',
				'NOT_NULL' => true,
				'VITAL'    => true,
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
				'GRADE' => Grades::I()->Pick([], null, ['{RANDOM}' => 'ASC']),
				'ROOM'  => Rooms::I()->Pick([], null, ['{RANDOM}' => 'ASC']),
				'START' => time() + $i * 3600,
			]);
		}
	}
}

