<?php

namespace System;

class TypeDate extends Type {
	public $name = 'Date';
	public $code = 'DATE';

	public function GetStructureStringType($info = []) {
		return 'date';
	}

	public function FormatInputValue($value, $info = []) {
		if (is_numeric($value)) {
			$value = date(\DateTime::ISO8601, $value);
		} else {
			$value = date(\DateTime::ISO8601, strtotime($value));
		}
		return $value;
	}

	public function FormatOutputValue($element, $code, $info) {
		$element[$code . '|TIMESTAMP'] = strtotime($element[$code]);
		return parent::FormatOutputValue($element, $code, $info);
	}
}
