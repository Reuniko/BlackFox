<?php

namespace System;

class TypeInner extends Type {
	public static $name = 'Inner';
	public static $code = 'INNER';

	public function GetStructureStringType() {
		throw new ExceptionType("No structure required");
	}

	public function PrepareSelectAndJoinByField($table, $prefix, $subfields) {
		// этот метод отвечает только за FIELDS, которые подтягиваются отдельно в методе HookExternalField
		return [];
	}

	/*
	public function PrepareSelectAndJoinByFilter($table, $prefix, $subfields) {
		// TODO integrate PrepareSelectAndJoinByFilter, doc
		if (empty($subfields)) {
			return [];
		}

		$RESULT = [
			'SELECT' => [],
			'JOIN'   => [],
		];

		$code = $this->info['CODE'];

		// @var SCRUD $Link //
		$Link = $this->info['LINK']::I();
		$external_prefix = $prefix . $code . "__";
		$raw_link_code = $external_prefix . $Link->code;

		// TODO replace '.ID' with real key:
		$RESULT['JOIN'][$raw_link_code] = "LEFT JOIN {$Link->code} AS {$raw_link_code} ON {$prefix}{$table}.ID = {$raw_link_code}.{$this->info['FIELD']}";
		$RESULT += $Link->PrepareSelectAndJoinByFields($subfields, $external_prefix);
		return $RESULT;
	}
	*/

	public function HookExternalField($elements, $subfields) {
		$code = $this->info['CODE'];
		$ids = array_keys($elements);

		foreach ($elements as $id => $element) {
			$elements[$id][$code] = [];
		}
		/** @var SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$link_key_to_source = $this->info['FIELD'];

		if (empty($subfields)) {
			$subfields = [$Link->key()];
		}
		$subfields[$link_key_to_source] = $link_key_to_source;

		$data = $Link->GetList([
			'FILTER' => [$link_key_to_source => $ids],
			'FIELDS' => $subfields,
		]);
		foreach ($data as $associative) {
			$ID = $associative[$link_key_to_source];
			unset($associative[$link_key_to_source]); // remove looking back identifier
			$elements[$ID][$code][$associative[$Link->key()]] = $associative;
		}

		return $elements;
	}

	public function GenerateJoinStatements(SCRUD $Current, $prefix) {
		// debug($this->info, '$this->info');
		/** @var SCRUD $Target */
		$Target = $this->info['LINK']::I();

		$current_alias = $prefix . $Current->code;
		$current_key = $Current->key();
		$target_alias = $this->info['CODE'] . '__' . $Target->code;
		$target_key = $this->info['FIELD'];

		$statement = "LEFT JOIN {$Target->code} AS {$target_alias} ON {$current_alias}.{$current_key} = {$target_alias}.{$target_key}";
		return [$target_alias => $statement];
	}
}