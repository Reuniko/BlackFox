<?php

namespace Testing;

class TestScrudInner extends Test {
	public $name = 'Тест SCRUD: внешние связи';

	/** @var Grades $Grades */
	public $Grades = null;
	/** @var Students $Students */
	public $Students = null;
	/** @var Rooms $Rooms */
	public $Rooms = null;
	/** @var Timetable $Timetable */
	public $Timetable = null;

	public function __construct() {
		parent::__construct();

		$this->Grades = Grades::I();
		$this->Grades->Synchronize();
		$this->Grades->Truncate();
		$this->Grades->Fill();

		$this->Students = Students::I();
		$this->Students->Synchronize();
		$this->Students->Truncate();
		$this->Students->Fill();

		$this->Rooms = Rooms::I();
		$this->Rooms->Synchronize();
		$this->Rooms->Truncate();
		$this->Rooms->Fill();

		$this->Timetable = Timetable::I();
		$this->Timetable->Synchronize();
		$this->Timetable->Truncate();
		$this->Timetable->Fill();
	}

	/** Тест чтения случайного элемента (студент), проверка структуры */
	public function TestReadStudent() {
		$student = $this->Students->Read(rand(100, 200));
		if (array_keys($student) <> ['ID', 'FIRST_NAME', 'LAST_NAME', 'GRADE']) {
			throw new Exception(['Wrong structure of $student', $student]);
		}
		if (array_keys($student['GRADE']) <> ['ID', 'TITLE']) {
			throw new Exception(['Wrong structure of $student[CLASS]', $student['GRADE']]);
		}
		//return $student;
	}

	/** Тест чтения случайного элемента (класс), проверка структуры */
	public function TestReadGrade() {
		$grade = $this->Grades->Read(rand(1, 20));
		if (array_keys($grade) <> ['ID', 'TITLE', 'CAPTAIN', 'STUDENTS', 'TIMETABLES']) {
			throw new Exception(['Wrong structure of $grade', $grade]);
		}
		if (array_keys(reset($grade['STUDENTS'])) <> ['ID', 'FIRST_NAME']) {
			throw new Exception(['Wrong structure of $grade->STUDENTS', reset($grade['STUDENTS'])]);
		}
		//return $grade;
	}

	/** Тест чтения случайного элемента со специфичной выборкой  */
	public function TestReadGradeStudents1A() {
		$grade = $this->Grades->Read(rand(1, 20), [
			'ID',
			'TITLE',
			'STUDENTS' => ['*'],
		]);
		if (array_keys($grade) <> ['ID', 'TITLE', 'STUDENTS']) {
			throw new Exception(['Wrong structure of $grade', $grade]);
		}
		if (array_keys(reset($grade['STUDENTS'])) <> ['ID', 'FIRST_NAME', 'LAST_NAME']) {
			throw new Exception(['Wrong structure of $grade->STUDENTS', reset($grade['STUDENTS'])]);
		}
		//return $grade;
	}

	/** Тест фильтра по цепочке: прямая связь */
	public function TestFilterStudentsByGrades() {
		$students = $this->Students->GetList([
			'FILTER' => ['GRADE.TITLE' => '9B'],
		]);
		foreach ($students as $student) {
			if ($student['GRADE']['TITLE'] <> '9B') {
				throw new Exception(['Wrong student', $student]);
			}
		}
		//return $students;
	}

	/** Тест фильтра по цепочке: прямая связь (в выборке отсутствуют поля фильтра) */
	public function TestFilterStudentsByGradesWithoutFilterField() {
		$students = $this->Students->GetList([
			'FILTER' => ['GRADE.TITLE' => '9B'],
			'FIELDS' => ['ID', 'FIRST_NAME'],
		]);
		foreach ($students as $student) {
			$data = $this->Students->Read($student['ID'], ['GRADE' => ['TITLE']]);
			if ($data['GRADE']['TITLE'] <> '9B') {
				throw new Exception(['Wrong student', $student]);
			}
		}
		// return $students;
	}

	/** Тест фильтра по цепочке: прямая связь, прямая связь */
	public function TestFilterStudentsByGradeCaptain() {
		// Carlota
		$students = $this->Students->GetList([
			'FILTER' => ['GRADE.CAPTAIN.FIRST_NAME' => 'Carlota'],
			'FIELDS' => ['ID', 'FIRST_NAME'],
		]);
		foreach ($students as $student) {
			$data = $this->Students->Read($student['ID'], ['GRADE' => ['CAPTAIN' => ['*']]]);
			if ($data['GRADE']['CAPTAIN']['FIRST_NAME'] <> 'Carlota') {
				throw new Exception(['Wrong student', $student]);
			}
		}
		// return $students;
	}

	/** Тест фильтра по цепочке: обратная связь, прямая связь */
	public function TestFilterGradesByRooms() {
		$grades = $this->Grades->GetList([
			'FIELDS' => ['ID', 'TITLE', 'TIMETABLES' => ['*@']],
			'FILTER' => ['TIMETABLES.ROOM.TITLE' => 'R-304'],
		]);
		foreach ($grades as $grade) {
			foreach ($grade['TIMETABLES'] as $timetable) {
				if ($timetable['ROOM']['TITLE'] === 'R-304') {
					continue 2;
				}
			}
			throw new Exception(['Wrong grade', $grade]);
		}
		// return $grades;
	}

}