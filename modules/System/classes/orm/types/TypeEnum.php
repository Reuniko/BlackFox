<?php

namespace System;

class TypeEnum extends AType {
	public $name = 'Enum';
	public $code = 'ENUM';

	public function GetStructureStringType($info = []) {
		return 'enum';
	}

	public function FormatValue($value, $info = []) {
		if (!isset($field['VALUES'][$value])) {
			throw new ExceptionType("Unknown enum value '{$value}' for field '{$info['NAME']}'");
		}
		return $value;
	}
}