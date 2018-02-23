<?php

namespace Testing;

class TestScrudInner extends Test {
	public $name = 'Тест SCRUD: внешние связи';

	/** @var \System\SCRUD $Classes */
	public $Classes = null;
	/** @var \System\SCRUD $Students */
	public $Students = null;

	public function __construct() {
		parent::__construct();

		$this->Classes = Classes::I();
		$this->Classes->Synchronize();
		//$this->Classes->Truncate();
		//$this->FillClasses();

		$this->Students = Students::I();
		$this->Students->Synchronize();
		// $this->Students->Truncate();
		// $this->FillStudents();
	}

	public function FillClasses() {
		foreach (['A', 'B', 'C'] as $class_letter) {
			foreach ([1, 2, 3, 4, 5, 7, 8, 9, 10, 11] as $class_number) {
				$this->Classes->Create(['TITLE' => $class_number . $class_letter]);
			}
		}
	}

	public function FillStudents() {
		$names = file(__DIR__ . '/data/names.txt', FILE_IGNORE_NEW_LINES);
		$lasts = ['J', 'G', 'V', 'X', 'Z'];
		for ($i = 0; $i < 800; $i++) {
			$this->Students->Create([
				'FIRST_NAME' => $names[array_rand($names)],
				'LAST_NAME'  => $lasts[array_rand($lasts)] . '.',
				'CLASS'      => $this->Classes->Pick([], ['RAND()' => 'ASC']),
			]);
		}
	}

	public function TestGetStudents() {
		$students = $this->Students->GetList([
			'SORT'  => ['RAND()' => 'ASC'],
			'LIMIT' => 3,
		]);
		return $students;
	}

	public function TestGetClasses() {
		$data = $this->Classes->Read(2, [
			'ID',
			'TITLE',
			'STUDENTS' => ['*@'],
		]);
		return $data;
	}

}