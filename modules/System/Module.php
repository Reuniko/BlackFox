<?php
namespace System;

class Module extends \System\AModule {
	public $name = 'Система';
	public $description = 'Главный системный модуль, предоставляющий основные инструменты для работы';
	public $version = '1.0';

	public function Upgrade() {
		Modules::I()->Synchronize();
		Users::I()->Synchronize();
		Files::I()->Synchronize();
		Groups::I()->Synchronize();
		Users2Groups::I()->Synchronize();
	}
}