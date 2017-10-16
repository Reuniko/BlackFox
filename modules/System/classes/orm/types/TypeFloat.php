<?php

namespace System;

class TypeFloat extends AType {
	public $name = 'Float';
	public $code = 'FLOAT';

	public function GetStructureStringType($info = []) {
		return 'float';
	}

	public function FormatValue($value, $info = []) {
		return (float)$value;
	}
}