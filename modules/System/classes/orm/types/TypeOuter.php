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

	public function PrepareSelectAndJoinByField($table, $prefix, $subfields) {
		if (empty($subfields)) {
			return parent::PrepareSelectAndJoinByField($table, $prefix, null);
		}
		$code = $this->info['CODE'];
		/** @var SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$external_prefix = $prefix . $code . "__";
		$raw_link_code = $external_prefix . $Link->code;

		$RESULT = $Link->PrepareSelectAndJoinByFields($subfields, $external_prefix);
		$RESULT['JOIN'][$raw_link_code] = "LEFT JOIN {$Link->code} AS {$raw_link_code} ON {$prefix}{$table}.{$code} = {$raw_link_code}.{$Link->key()}";
		return $RESULT;
	}
}