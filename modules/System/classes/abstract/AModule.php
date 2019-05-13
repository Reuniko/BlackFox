<?php

namespace System;
abstract class AModule extends Instanceable {
	public $name = 'New unknown module';
	public $description = 'Redefine $name, $description и $version';
	public $version = '1.0';

	public function Install() {
		$class = end(explode('\\', static::class));
		if (Modules::Instance()->Present($class)) {
			throw new Exception(T([
				'en' => "Module '{$class}' already installed",
				'ru' => "Модуль '{$class}' уже установлен",
			]));
		} else {
			Modules::Instance()->Create([
				'ID'          => $class,
				'NAME'        => $this->name,
				'DESCRIPTION' => $this->description,
				'VERSION'     => $this->version,
			]);
		}
		$this->Upgrade();
	}

	public function Uninstall() {
		$namespace = reset(explode('\\', static::class));
		Modules::Instance()->Delete($namespace);
	}

	public function Upgrade() {
		// override
	}

	public function Load() {
		// override
	}

	/**
	 * Compiles and return the tree of menu for administrative section
	 * See children for examples
	 *
	 * @return array
	 */
	public function Menu() {
		return [];
	}
}