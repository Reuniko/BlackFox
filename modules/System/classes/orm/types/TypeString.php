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

	/**
	 * Deleting all extra spaces
	 *
	 * @param string $value
	 * @return string
	 */
	public function FormatInputValue($value) {
		return trim(mb_ereg_replace('#\s+#', ' ', $value));
	}
}