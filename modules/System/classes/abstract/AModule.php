<?php
namespace System;
abstract class AModule extends Instanceable {
	public $name = 'Новый неизвестный модуль';
	public $description = 'Переопределите поля $name, $description и $version';
	public $version = '1.0';

	public function Install() {
		$class = end(explode('\\', static::class));
		if (Unit::Instance()->Present($class)) {
			throw new Exception("Module '{$class}' already installed");
		} else {
			Unit::Instance()->Create([
				'ID'          => $class,
				'NAME'        => $this->name,
				'DESCRIPTION' => $this->description,
				'VERSION'     => $this->version,
			]);
		}
		$this->Upgrade();
	}

	public function Uninstall() {
		$class = end(explode('\\', static::class));
		Unit::Instance()->Delete($class);
	}

	public function Upgrade() {
		// override
	}

	public function Load() {
		// override
	}
}