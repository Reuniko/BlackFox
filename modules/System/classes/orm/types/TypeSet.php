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
			if (!isset($info['VALUES'][$value])) {
				throw new ExceptionType("Unknown set value '{$value}' for field '{$info['NAME']}'");
			}
		}
		$value = implode(',', $values);
		return $value;
	}

	public function FormatOutputValue($element, $code, $info) {
		if (empty($element[$code])) {
			$element[$code] = [];
		} else {
			$element[$code] = explode(",", $element[$code]);
		}
		$element["$code|VALUES"] = [];
		foreach ($element["$code"] as $key) {
			$element["$code|VALUES"][$key] = $info['VALUES'][$key];
		}
		return $element;
	}
}