<?php

namespace System;

class TypeNumber extends Type {
	public static $name = 'Number';
	public static $code = 'NUMBER';

	public function GetStructureStringType() {
		return 'int';
	}

	public function FormatInputValue($value) {
		return (int)$value;
	}
}