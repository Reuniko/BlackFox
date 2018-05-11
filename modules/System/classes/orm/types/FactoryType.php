<?php

namespace System;

class FactoryType extends Instanceable {

	public $types = [];

	public function __construct() {
		// TODO Cache the result
		foreach (Engine::I()->classes as $class_name => $class_path) {
			if (in_array('System\Type', class_parents($class_name))) {
				/** @var Type $class_name */
				$this->types[$class_name::$code] = $class_name;
			}
		}
	}

	/**
	 * Get instance of class mapped to code of the type
	 *
	 * @param array $info symbolic code of the type
	 * @return Type instance of class
	 * @throws ExceptionType Class not found
	 */
	public function Get(array $info) {
		if (!isset($this->types[$info['TYPE']])) {
			throw new ExceptionType("Class for type '{$info['TYPE']}' not found");
		}
		/** @var Type $class */
		$class = $this->types[$info['TYPE']];
		return new $class($info);
	}
}

