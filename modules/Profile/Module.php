<?php
namespace Profile;

class Module extends \System\AbstractModule {
	public $name = 'Профиль';
	public $description = 'Профиль пользователя, дополнительные поля, аватар';
	public $version = '1.0';

	public function Upgrade() {
		Users::Instance()->Synchronize();
	}

	public function Load() {
		// \System\Engine::Instance()->Debug("Module 'Profile' loaded");
	}


}