<?php

namespace System;

class TypeNumber extends AType {
	public $name = 'Number';
	public $code = 'NUMBER';

	public function GetStructureStringType($info = []) {
		return 'int';
	}

	public function FormatValue($value, $info = []) {
		return (int)$value;
	}
}