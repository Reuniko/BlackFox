<?php

namespace System;

class TypeTime extends Type {
	public $name = 'Time';
	public $code = 'TIME';

	public function GetStructureStringType($info = []) {
		return 'time';
	}
}