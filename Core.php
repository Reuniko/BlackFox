<?php

namespace BlackFox;

class Core extends \BlackFox\ACore {
	public $name = 'BlackFox';
	public $description = 'Main system module providing basic tools';
	public $version = '1.0';

	public function GetScheme() {
		return new Scheme([
			Users::I(),
			Files::I(),
			Groups::I(),
			Users2Groups::I(),
			Pages::I(),
			Redirects::I(),
			Log::I(),
			TableSettings::I(),
		]);
	}

	public function Upgrade() {
		$this->GetScheme()->Synchronize();
		Cache::I()->Clear();
	}

	public function Menu() {
		return [
			'BlackFox' => [
				'NAME'     => T([
					'en' => 'System',
					'ru' => 'Система',
				]),
				'EXPANDER' => true,
				'CHILDREN' => [
					'index'                  => [
						'NAME'     => T([
							'en' => 'Control panel',
							'ru' => 'Панель управления',
						]),
						'LINK'     => '/admin/BlackFox/',
						'CHILDREN' => [
							'SchemeSynchronizer' => [
								'NAME' => T([
									'en' => 'Scheme synchronizer',
									'ru' => 'Синхронизатор схем',
								]),
								'LINK' => '/admin/BlackFox/SchemeSynchronizer.php',
							],
							'PHPConsole'         => [
								'NAME' => T([
									'en' => 'PHP console',
									'ru' => 'PHP консоль',
								]),
								'LINK' => '/admin/BlackFox/PHPConsole.php',
							],
							'SQLConsole'         => [
								'NAME' => T([
									'en' => 'SQL console',
									'ru' => 'SQL консоль',
								]),
								'LINK' => '/admin/BlackFox/SQLConsole.php',
							],
							'MaterialDesignIcons'         => [
								'NAME' => T([
									'en' => 'Material Design Icons',
									'ru' => 'Material Design Icons',
								]),
								'LINK' => '/admin/BlackFox/MaterialDesignIcons.php',
							],
						],
					],
					'BlackFox_Content'       => [
						'NAME' => T([
							'en' => 'Content pages',
							'ru' => 'Контентные страницы',
						]),
						'LINK' => '/admin/BlackFox/Pages.php',
					],
					'BlackFox_Redirects'     => [
						'NAME' => T([
							'en' => 'Redirects',
							'ru' => 'Редиректы',
						]),
						'LINK' => '/admin/BlackFox/Redirects.php',
					],
					'BlackFox_Users'         => [
						'NAME' => T([
							'en' => 'Users',
							'ru' => 'Пользователи',
						]),
						'LINK' => '/admin/BlackFox/Users.php',
					],
					'BlackFox_Groups'        => [
						'NAME' => T([
							'en' => 'Groups',
							'ru' => 'Группы',
						]),
						'LINK' => '/admin/BlackFox/Groups.php',
					],
					'BlackFox_Users2Groups'  => [
						'NAME' => T([
							'en' => 'Users in groups',
							'ru' => 'Пользователи в группах',
						]),
						'LINK' => '/admin/BlackFox/Users2Groups.php',
					],
					'BlackFox_Files'         => [
						'NAME' => T([
							'en' => 'Files',
							'ru' => 'Файлы',
						]),
						'LINK' => '/admin/BlackFox/Files.php',
					],
					'BlackFox_Log'           => [
						'NAME' => T([
							'en' => 'Log',
							'ru' => 'Журнал',
						]),
						'LINK' => '/admin/BlackFox/Log.php',
					],
					'BlackFox_TableSettings' => [
						'NAME' => T([
							'en' => 'Table settings',
							'ru' => 'Настройки таблиц',
						]),
						'LINK' => '/admin/BlackFox/TableSettings.php',
					],
				],
			],
		];
	}
}