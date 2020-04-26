<?php

namespace BlackFox;

class FactoryType {

	use Instance;

	public $TYPES = [];

	public function __construct() {
		foreach (Engine::I()->classes as $class_name => $class_path) {
			if (in_array('BlackFox\Type', class_parents($class_name))) {
				/** @var \BlackFox\Type $class_name */
				$this->TYPES[$class_name::$TYPE] = $class_name;
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
		$info['TYPE'] = strtoupper($info['TYPE']);
		if (!isset($this->TYPES[$info['TYPE']])) {
			throw new ExceptionType("Class for type '{$info['TYPE']}' not found");
		}
		/** @var Type $class */
		$class = $this->TYPES[$info['TYPE']];
		return new $class($info);
	}
}

