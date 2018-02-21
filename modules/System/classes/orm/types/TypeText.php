<?php

namespace System;

class TypeText extends Type {
	public static $name = 'Text';
	public static $code = 'TEXT';

	public function GetStructureStringType() {
		return 'text';
	}
}