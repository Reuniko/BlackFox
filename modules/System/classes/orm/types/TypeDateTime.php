<?php

namespace System;

class TypeDateTime extends Type {
	public $name = 'DateTime';
	public $code = 'DATETIME';

	public function GetStructureStringType($info = []) {
		return 'datetime';
	}

	public function FormatInputValue($value, $info = []) {
		if (is_numeric($value)) {
			$value = date(\DateTime::ISO8601, $value);
		} else {
			$value = date(\DateTime::ISO8601, strtotime($value));
		}
		return $value;
	}
}