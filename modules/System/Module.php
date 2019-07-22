<?php

namespace System;

class Module extends \System\AModule {
	public $name = 'System';
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

		Cache::I()->Clear();
	}

	public function Menu() {
		return [
			'System' => [
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
						'LINK'     => '/admin/System/Panel.php',
						'CHILDREN' => [
							'~upgrade' => [
								'NAME' => '~upgrade',
								'LINK' => '/admin/_upgrade.php',
							],
						],
					],
					'System_Content'      => [
						'NAME' => T([
							'en' => 'Content pages',
							'ru' => 'Контентные страницы',
						]),
						'LINK' => '/admin/System/Content.php',
					],
					'System_Redirects'    => [
						'NAME' => T([
							'en' => 'Redirects',
							'ru' => 'Редиректы',
						]),
						'LINK' => '/admin/System/Redirects.php',
					],
					'System_Users'        => [
						'NAME' => T([
							'en' => 'Users',
							'ru' => 'Пользователи',
						]),
						'LINK' => '/admin/System/Users.php',
					],
					'System_Groups'       => [
						'NAME' => T([
							'en' => 'Groups',
							'ru' => 'Группы',
						]),
						'LINK' => '/admin/System/Groups.php',
					],
					'System_Users2Groups' => [
						'NAME' => T([
							'en' => 'Users in groups',
							'ru' => 'Пользователи в группах',
						]),
						'LINK' => '/admin/System/Users2Groups.php',
					],
					'System_Files'        => [
						'NAME' => T([
							'en' => 'Files',
							'ru' => 'Файлы',
						]),
						'LINK' => '/admin/System/Files.php',
					],
					'System_Log'          => [
						'NAME' => T([
							'en' => 'Log',
							'ru' => 'Журнал',
						]),
						'LINK' => '/admin/System/Log.php',
					],
				],
			],
		];
	}
}