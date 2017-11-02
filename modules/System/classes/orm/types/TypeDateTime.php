<?php

namespace System;

class TypeDateTime extends TypeDate {
	public $name = 'DateTime';
	public $code = 'DATETIME';

	public function GetStructureStringType($info = []) {
		return 'datetime';
	}
}