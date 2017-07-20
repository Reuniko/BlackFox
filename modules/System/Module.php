<?php
namespace System;

class Module extends \System\AbstractModule {
	public $name = 'Система';
	public $description = 'Главный системный модуль, предоставляющий основные инструменты для работы';
	public $version = '1.0';

	public function Upgrade() {
		Users::Instance()->Synchronize();
		Modules::Instance()->Synchronize();
		Files::Instance()->Synchronize();
	}
}