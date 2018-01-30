<?php
namespace Admin;

class Module extends \System\AModule {
	public $name = 'Администрирование';
	public $description = 'Классы и компоненты, используемые в административной части';
	public $version = '1.0';

	public function Upgrade() {
		TableSettings::I()->Synchronize();
	}

	public function Load() {
		// \System\Engine::Instance()->Debug("Module 'Profile' loaded");
	}


}