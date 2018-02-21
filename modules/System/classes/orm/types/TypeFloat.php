<?php

namespace System;

class TypeFloat extends Type {
	public static $name = 'Float';
	public static $code = 'FLOAT';

	public function GetStructureStringType() {
		return 'float';
	}

	public function FormatInputValue($value) {
		return (float)$value;
	}
}