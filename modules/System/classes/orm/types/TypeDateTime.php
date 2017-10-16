<?php

namespace System;

class TypeDateTime extends AType {
	public $name = 'DateTime';
	public $code = 'DATETIME';

	public function GetStructureStringType($info = []) {
		return 'datetime';
	}

	public function FormatValue($value, $info = []) {
		if (is_numeric($value)) {
			$value = date(\DateTime::ISO8601, $value);
		} else {
			$value = date(\DateTime::ISO8601, strtotime($value));
		}
		return $value;
	}
}