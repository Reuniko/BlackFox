<?php

namespace System;

class TypeText extends Type {
	public $name = 'Text';
	public $code = 'TEXT';

	public function GetStructureStringType($info = []) {
		return 'text';
	}
}