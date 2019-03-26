<?php

namespace System;

class Module extends \System\AModule {
	public $name = 'Система';
	public $description = 'Главный системный модуль, предоставляющий основные инструменты для работы';
	public $version = '1.0';

	public function Upgrade() {

		Users2Groups::I()->DropConstraints();

		Modules::I()->Synchronize();
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
}