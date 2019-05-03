<?php

namespace Testing;

class Module extends \System\AModule {
	public $name = 'Testing';
	public $description = 'A system module that provides testing tools and tests for core components';
	public $version = '1.0';

	public function Upgrade() {

		Grades::I()->DropConstraints();
		Timetable::I()->DropConstraints();
		Students::I()->DropConstraints();

		Grades::I()->Synchronize();
		Rooms::I()->Synchronize();
		Timetable::I()->Synchronize();
		Students::I()->Synchronize();

		Grades::I()->CreateConstraints();
		Timetable::I()->CreateConstraints();
		Students::I()->CreateConstraints();
	}
}