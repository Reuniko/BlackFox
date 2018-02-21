<?php

namespace System;

class TypeDate extends Type {
	public static $name = 'Date';
	public static $code = 'DATE';

	public function GetStructureStringType() {
		return 'date';
	}

	public function FormatInputValue($value) {
		if (is_numeric($value)) {
			$value = date('Y-m-d', $value);
		} else {
			$value = date('Y-m-d', strtotime($value));
		}
		return $value;
	}

	public function FormatOutputValue($element) {
		$code = $this->info['CODE'];
		$element[$code . '|TIMESTAMP'] = strtotime($element[$code]);
		return $element;
	}
}
