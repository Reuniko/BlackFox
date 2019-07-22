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

	public function Menu() {
		return [
			'Admin' => [
				'NAME'     => T([
					'en' => 'Admin',
					'ru' => 'Админка',
				]),
				'EXPANDER' => true,
				'CHILDREN' => [
					'Admin_TableSettings' => [
						'NAME' => T([
							'en' => 'Table settings',
							'ru' => 'Настройки таблиц',
						]),
						'LINK' => '/admin/Admin/TableSettings.php',
					],
					'Admin_PHPConsole'    => [
						'NAME' => T([
							'en' => 'PHP console',
							'ru' => 'PHP консоль',
						]),
						'LINK' => '/admin/Admin/PHPConsole.php',
					],
				],
			],
		];
	}

}