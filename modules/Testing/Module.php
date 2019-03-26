<?php

namespace Testing;

class Module extends \System\AModule {
	public $name = 'Тестирование';
	public $description = 'Системный модуль, предоставляющий инструменты для тестирования и тесты основных компонентов ядра';
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