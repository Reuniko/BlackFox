<?php

namespace System;

class TypeLink extends AType {
	public $name = 'Link';
	public $code = 'LINK';

	public function GetStructureStringType($info = []) {
		return 'int';
	}

	public function FormatValue($value, $info = []) {
		return (int)$value;
	}
}