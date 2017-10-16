<?php

namespace System;

class TypeFloat extends Type {
	public $name = 'Float';
	public $code = 'FLOAT';

	public function GetStructureStringType($info = []) {
		return 'float';
	}

	public function FormatInputValue($value, $info = []) {
		return (float)$value;
	}
}