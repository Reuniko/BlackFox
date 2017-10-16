<?php

namespace System;

class FactoryType extends Instanceable {

	public $types = [];

	public function Init() {
		parent::Init();
		foreach (Engine::I()->classes as $class_name => $class_path) {
			if (in_array('System\Type', class_parents($class_name))) {
				/** @var Type $class_name */
				$code = $class_name::I()->code;
				$this->types[$code] = $class_name;
			}
		}
	}

	/**
	 * @param string $code
	 * @return Type
	 * @throws ExceptionType
	 */
	public function Get($code) {
		if (!isset($this->types[$code])) {
			throw new ExceptionType("Class for type '{$code}' not found");
		}
		/** @var Type $class */
		$class = $this->types[$code];
		return $class::I();
	}
}

