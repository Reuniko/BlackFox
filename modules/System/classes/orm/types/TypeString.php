<?php

namespace System;

class TypeString extends AType {
	public $name = 'String';
	public $code = 'STRING';
	public $default_length = 255;

	public function GetStructureStringType($info = []) {
		$length = (int)$info['LENGTH'] ?: $this->default_length;
		return "varchar({$length})";
	}

	public function FormatValue($value, $info = []) {
		$value = preg_replace('#\s+#', ' ', $value);
		$value = trim($value);
		return $value;
	}
}