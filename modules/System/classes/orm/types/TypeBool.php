<?php

namespace System;

class TypeBool extends Type {
	public static $name = 'Bool';
	public static $code = 'BOOL';

	public function GetStructureStringType() {
		return 'bool';
	}

	public function ProvideInfoIntegrity($info = []) {
		$info['NOT_NULL'] = true;
		return $info;
	}
}