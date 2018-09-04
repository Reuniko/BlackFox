<?php

namespace System;

class TypeFile extends TypeOuter {
	public static $name = 'File';
	public static $code = 'FILE';

	public function GetStructureStringType() {
		return 'int';
	}

	public function ProvideInfoIntegrity($info = []) {
		if (empty($info['LINK'])) {
			$info['LINK'] = 'System\Files';
		}
		return $info;
	}

	public function FormatInputValue($value) {
		if (is_numeric($value)) {
			return (int)$value;
		}
		if (is_array($value)) {
			return $this->info['LINK']::I()->Create($value);
		}
		return null;
	}
}