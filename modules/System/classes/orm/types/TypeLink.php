<?php

namespace System;

class TypeLink extends Type {
	public $name = 'Link';
	public $code = 'LINK';

	public function GetStructureStringType($info = []) {
		return 'int';
	}

	public function FormatInputValue($value, $info = []) {
		return (int)$value;
	}
}