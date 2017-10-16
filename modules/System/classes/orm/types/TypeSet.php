<?php

namespace System;

class TypeSet extends Type {
	public $name = 'Set';
	public $code = 'SET';

	public function GetStructureStringType($info = []) {
		return 'set' . '("' . implode('", "', array_keys($info['VALUES'])) . '")';
	}

	public function FormatInputValue($values, $info = []) {
		if (!is_array($values)) {
			$values = [$values];
		}
		foreach ($values as $value) {
			if (!isset($field['VALUES'][$value])) {
				throw new ExceptionType("Unknown set value '{$value}' for field '{$info['NAME']}'");
			}
		}
		$value = implode(',', $values);
		return $value;
	}
}