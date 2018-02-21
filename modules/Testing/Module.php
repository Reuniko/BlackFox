<?php
namespace Testing;

class Module extends \System\AModule {
	public $name = 'Тестирование';
	public $description = 'Системный модуль, предоставляющий инструменты для тестирования и тесты основных компонентов ядра';
	public $version = '1.0';

	public function Upgrade() {
	}
}