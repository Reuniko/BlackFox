<?php

namespace System;

class TypeText extends AType {
	public $name = 'Text';
	public $code = 'TEXT';

	public function GetStructureStringType($info = []) {
		return 'text';
	}
}