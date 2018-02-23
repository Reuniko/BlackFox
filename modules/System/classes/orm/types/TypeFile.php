<?php

namespace System;

class TypeFile extends TypeOuter {
	public static $name = 'File';
	public static $code = 'FILE';

	public function GetStructureStringType() {
		return 'int';
	}

	public function ProvideInfoIntegrity($info = []) {
		$info['LINK'] = $info['LINK'] ?: '\System\Files';
		return $info;
	}

	public function FormatInputValue($value) {
		if (is_array($value)) {
			$value = $this->info['LINK']::I()->Create($value);
		}
		return (int)$value;
	}
}