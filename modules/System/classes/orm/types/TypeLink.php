<?php

namespace System;

class TypeLink extends Type {
	public $name = 'Link';
	public $code = 'LINK';

	public function GetStructureStringType($info = []) {
		return 'int';
	}

	public function FormatInputValue($value, $info = []) {
		return (int)$value;
	}

	public function FormatOutputValue($element, $code, $info) {
		/** @var SCRUD $link */
		$link = $info["LINK"];
		if (!in_array('System\SCRUD', class_parents($link))) {
			throw new ExceptionType("Field '{$code}': link '{$link}' must be SCRUD child ");
		}
		$element[$code] = $link::I()->FormatOutputValues($element[$code]);
		return $element;
	}
}