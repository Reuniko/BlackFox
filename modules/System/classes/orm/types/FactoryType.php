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
	 * Get instance of class mapped to code of the type
	 *
	 * @param string $type symbolic code of the type
	 * @return Type instance of class
	 * @throws ExceptionType Class not found
	 */
	public function Get($type) {
		if (!isset($this->types[$type])) {
			throw new ExceptionType("Class for type '{$type}' not found");
		}
		/** @var Type $class */
		$class = $this->types[$type];
		return $class::I();
	}
}

