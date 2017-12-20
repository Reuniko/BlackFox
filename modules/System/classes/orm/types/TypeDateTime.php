<?php

namespace System;

class TypeDateTime extends TypeDate {
	public $name = 'DateTime';
	public $code = 'DATETIME';

	public function GetStructureStringType($info = []) {
		return 'datetime';
	}

	public function GetStructureString($code, $info = []) {
		$string = parent::GetStructureString($code, $info);
		if ($info['TRIGGER'] === 'CREATE') {
			$string = "{$string} DEFAULT CURRENT_TIMESTAMP";
		}
		if ($info['TRIGGER'] === 'UPDATE') {
			$string = "{$string} ON UPDATE CURRENT_TIMESTAMP";
		}
		return $string;
	}
}