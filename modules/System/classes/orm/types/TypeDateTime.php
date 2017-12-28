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
			$value = date('Y-m-d H:i:s', $value);
		} else {
			$value = date('Y-m-d H:i:s', strtotime($value));
		}
		return $value;
	}

	public function FormatOutputValue($element, $code, $info) {
		$element[$code . '|TIMESTAMP'] = strtotime($element[$code]);
		return parent::FormatOutputValue($element, $code, $info);
	}

	public function GetStructureString($code, $info = []) {
		$string = parent::GetStructureString($code, $info);
		if ($info['TRIGGER'] === 'CREATE') {
			$string = "{$string} DEFAULT CURRENT_TIMESTAMP";
		}
		if ($info['TRIGGER'] === 'UPDATE') {
			$string = "{$string} ON UPDATE CURRENT_TIMESTAMP";
		}
		return $string;
	}
}