<?php
namespace System;

class Module extends \System\AModule {
	public $name = 'Система';
	public $description = 'Главный системный модуль, предоставляющий основные инструменты для работы';
	public $version = '1.0';

	public function Upgrade() {
		Unit::I()->Synchronize();
		User::I()->Synchronize();
		File::I()->Synchronize();
		Group::I()->Synchronize();
		User2Group::I()->Synchronize();
	}
}