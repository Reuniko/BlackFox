<?php

namespace System;

class TypeEnum extends Type {
	public $name = 'Enum';
	public $code = 'ENUM';

	public function GetStructureStringType($info = []) {
		return 'enum';
	}

	public function FormatInputValue($value, $info = []) {
		if (!isset($info['VALUES'][$value])) {
			throw new ExceptionType("Unknown enum value '{$value}' for field '{$info['NAME']}'");
		}
		return $value;
	}
}