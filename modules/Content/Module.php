<?php

namespace Content;

class Module extends \System\AModule {
	public $name = 'Контент';
	public $description = 'Контентный системный модуль, предоставляющий инструменты для создания стратических страниц';
	public $version = '1.0';

	public function Upgrade() {
		Pages::I()->Synchronize();
	}
}