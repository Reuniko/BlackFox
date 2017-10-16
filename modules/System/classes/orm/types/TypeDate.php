<?php

namespace System;

class TypeDate extends AType {
	public $name = 'Date';
	public $code = 'DATE';

	public function GetStructureStringType($info = []) {
		return 'date';
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
