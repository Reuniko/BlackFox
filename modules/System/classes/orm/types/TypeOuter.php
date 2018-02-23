<?php

namespace System;

class TypeOuter extends Type {
	public static $name = 'Outer';
	public static $code = 'OUTER';

	public function GetStructureStringType() {
		return 'int';
	}

	public function FormatInputValue($value) {
		return (int)$value;
	}

	public function FormatOutputValue($element) {
		/** @var SCRUD $link */
		$link = $this->info['LINK'];
		$code = $this->info['CODE'];
		if (!in_array('System\SCRUD', class_parents($link))) {
			throw new ExceptionType("Field '{$code}': link '{$link}' must be SCRUD child ");
		}
		$element[$code] = $link::I()->FormatOutputValues($element[$code]);
		return $element;
	}
}