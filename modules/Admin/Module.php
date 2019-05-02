<?php

namespace Admin;

class Module extends \System\AModule {
	public $name = 'Admin';
	public $description = 'Classes and units used in administrative section';
	public $version = '1.0';

	public function Upgrade() {
		TableSettings::I()->Synchronize();
	}

	public function Load() {
		// \System\Engine::Instance()->Debug("Module 'Profile' loaded");
	}


}