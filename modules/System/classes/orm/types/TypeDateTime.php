<?php

namespace System;

class TypeDateTime extends Type {
	public static $name = 'DateTime';
	public static $code = 'DATETIME';

	public function GetStructureStringType() {
		return 'datetime';
	}

	public function FormatInputValue($value) {
		if (is_numeric($value)) {
			$value = date('Y-m-d H:i:s', $value);
		} else {
			$value = date('Y-m-d H:i:s', strtotime($value));
		}
		return $value;
	}

	public function FormatOutputValue($element) {
		$code = $this->info['CODE'];
		$element[$code . '|TIMESTAMP'] = strtotime($element[$code]);
		return $element;
	}

	public function GetStructureString() {
		$string = parent::GetStructureString();
		if ($this->info['TRIGGER'] === 'CREATE') {
			$string = "{$string} DEFAULT CURRENT_TIMESTAMP";
		}
		if ($this->info['TRIGGER'] === 'UPDATE') {
			$string = "{$string} ON UPDATE CURRENT_TIMESTAMP";
		}
		return $string;
	}
}