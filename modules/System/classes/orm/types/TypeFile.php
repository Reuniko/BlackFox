<?php

namespace System;

class TypeFile extends TypeLink {
	public $name = 'File';
	public $code = 'FILE';

	public function GetStructureStringType($info = []) {
		return 'int';
	}

	public function ProvideInfoIntegrity($info = []) {
		$info['LINK'] = $info['LINK'] ?: 'System\File';
		return $info;
	}

	public function FormatInputValue($value, $info = []) {
		if (is_array($value)) {
			$value = $info['LINK']::I()->Create($value);
		}
		return (int)$value;
	}
}