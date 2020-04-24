<?php

namespace BlackFox;

class Core extends \BlackFox\ACore {
	public $name = 'BlackFox';
	public $description = 'Main system module providing basic tools';
	public $version = '1.0';

	public function Upgrade() {

		Users2Groups::I()->DropConstraints();

		Users::I()->Synchronize();
		Files::I()->Synchronize();
		Groups::I()->Synchronize();
		Users2Groups::I()->Synchronize();
		Content::I()->Synchronize();
		Redirects::I()->Synchronize();
		Log::I()->Synchronize();

		Users2Groups::I()->CreateConstraints();

		TableSettings::I()->Synchronize();

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
					'panel'               => [
						'NAME'     => T([
							'en' => 'Control panel',
							'ru' => 'Панель управления',
						]),
						'LINK'     => '/admin/BlackFox/Panel.php',
						'CHILDREN' => [
							'~upgrade' => [
								'NAME' => '~upgrade',
								'LINK' => '/admin/_upgrade.php',
							],
						],
					],
					'BlackFox_Content'      => [
						'NAME' => T([
							'en' => 'Content pages',
							'ru' => 'Контентные страницы',
						]),
						'LINK' => '/admin/BlackFox/Content.php',
					],
					'BlackFox_Redirects'    => [
						'NAME' => T([
							'en' => 'Redirects',
							'ru' => 'Редиректы',
						]),
						'LINK' => '/admin/BlackFox/Redirects.php',
					],
					'BlackFox_Users'        => [
						'NAME' => T([
							'en' => 'Users',
							'ru' => 'Пользователи',
						]),
						'LINK' => '/admin/BlackFox/Users.php',
					],
					'BlackFox_Groups'       => [
						'NAME' => T([
							'en' => 'Groups',
							'ru' => 'Группы',
						]),
						'LINK' => '/admin/BlackFox/Groups.php',
					],
					'BlackFox_Users2Groups' => [
						'NAME' => T([
							'en' => 'Users in groups',
							'ru' => 'Пользователи в группах',
						]),
						'LINK' => '/admin/BlackFox/Users2Groups.php',
					],
					'BlackFox_Files'        => [
						'NAME' => T([
							'en' => 'Files',
							'ru' => 'Файлы',
						]),
						'LINK' => '/admin/BlackFox/Files.php',
					],
					'BlackFox_Log'          => [
						'NAME' => T([
							'en' => 'Log',
							'ru' => 'Журнал',
						]),
						'LINK' => '/admin/BlackFox/Log.php',
					],
					'Admin_TableSettings' => [
						'NAME' => T([
							'en' => 'Table settings',
							'ru' => 'Настройки таблиц',
						]),
						'LINK' => '/admin/BlackFox/TableSettings.php',
					],
					'Admin_PHPConsole'    => [
						'NAME' => T([
							'en' => 'PHP console',
							'ru' => 'PHP консоль',
						]),
						'LINK' => '/admin/BlackFox/PHPConsole.php',
					],
				],
			],
		];
	}
}