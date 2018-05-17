<?php

namespace System;

class TypeFloat extends Type {
	public static $name = 'Float';
	public static $code = 'FLOAT';

	public function GetStructureStringType() {
		$length = $this->info['LENGTH'] ?: 13;
		$decimals = $this->info['DECIMALS'] ?: 2;
		return "float({$length},{$decimals})";
	}

	public function FormatInputValue($value) {
		return str_replace(',', '.', (float)$value);
	}
}