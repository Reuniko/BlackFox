<?php

namespace System;

class TypeString extends Type {
	public static $name = 'String';
	public static $code = 'STRING';
	const DEFAULT_LENGTH = 255;

	public function GetStructureStringType() {
		$length = (int)$this->info['LENGTH'] ?: self::DEFAULT_LENGTH;
		return "varchar({$length})";
	}

	public function FormatInputValue($value) {
		$value = preg_replace('#\s+#', ' ', $value);
		$value = trim($value);
		return $value;
	}
}