<?php

namespace Testing;

class Timetable extends \System\SCRUD {
	public function Init() {
		$this->name = 'Расписание занятий';
		$this->structure = [
			'ID'       => self::ID,
			'ROOM'     => [
				'TYPE'     => 'OUTER',
				'NAME'     => 'Аудитория',
				'LINK'     => 'Rooms',
				'NOT_NULL' => true,
				'FOREIGN'  => 'CASCADE',
			],
			'GRADE'    => [
				'TYPE'     => 'OUTER',
				'NAME'     => 'Класс',
				'LINK'     => 'Grades',
				'NOT_NULL' => true,
				'FOREIGN'  => 'CASCADE',
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

	public function Fill($total) {
		for ($i = 0; $i < $total; $i++) {
			$this->Create([
				'GRADE' => Grades::I()->Pick([], null, ['{RANDOM}' => 'ASC']),
				'ROOM'  => Rooms::I()->Pick([], null, ['{RANDOM}' => 'ASC']),
				'START' => time() + $i * 3600,
			]);
		}
	}
}

