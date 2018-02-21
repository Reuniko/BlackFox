<?php

namespace System;

class TypeTime extends Type {
	public static $name = 'Time';
	public static $code = 'TIME';

	public function GetStructureStringType() {
		return 'time';
	}
}