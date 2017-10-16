<?php

namespace System;

class TypeNumber extends Type {
	public $name = 'Number';
	public $code = 'NUMBER';

	public function GetStructureStringType($info = []) {
		return 'int';
	}

	public function FormatInputValue($value, $info = []) {
		return (int)$value;
	}
}