<?php

namespace System;

class TypeBool extends Type {
	public $name = 'Bool';
	public $code = 'BOOL';

	public function GetStructureStringType($info = []) {
		return 'bool';
	}

	public function ProvideInfoIntegrity($info = []) {
		$info['NOT_NULL'] = true;
		return $info;
	}
}