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
		/** @var SCRUD $Link */
		$Link = $this->info['LINK'];
		$code = $this->info['CODE'];
		if (!in_array('System\SCRUD', class_parents($Link))) {
			throw new ExceptionType("Field '{$code}': link '{$Link}' must be SCRUD child ");
		}
		$element[$code] = $Link::I()->FormatOutputValues($element[$code]);
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
		$join = "LEFT JOIN {$Link->code} AS {$raw_link_code} ON {$prefix}{$table}.{$code} = {$raw_link_code}.{$Link->key()}";
		$RESULT['JOIN'] = array_merge(
			[$raw_link_code => $join],
			$RESULT['JOIN']
		);
		return $RESULT;
	}

	public function GenerateJoinStatements(SCRUD $Current, $prefix) {
		// debug($this->info, '$this->info');
		/** @var SCRUD $Target */
		$Target = $this->info['LINK']::I();

		$current_alias = $prefix . $Current->code;
		$current_key = $this->info['CODE'];
		$target_alias = $this->info['CODE'] . '__' . $Target->code;
		$target_key = $Target->key();

		$statement = "LEFT JOIN {$Target->code} AS {$target_alias} ON {$current_alias}.{$current_key} = {$target_alias}.{$target_key}";
		return [$target_alias => $statement];
	}
}