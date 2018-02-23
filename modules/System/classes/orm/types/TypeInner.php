<?php

namespace System;

class TypeInner extends Type {
	public static $name = 'Inner';
	public static $code = 'INNER';

	public function GetStructureStringType() {
		throw new ExceptionType("No structure required");
	}
}