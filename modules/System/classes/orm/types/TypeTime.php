<?php

namespace System;

class TypeTime extends AType {
	public $name = 'Time';
	public $code = 'TIME';

	public function GetStructureStringType($info = []) {
		return 'time';
	}
}