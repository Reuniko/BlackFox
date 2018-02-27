<?php

namespace System;

class TypeInner extends Type {
	public static $name = 'Inner';
	public static $code = 'INNER';

	public function GetStructureStringType() {
		throw new ExceptionType("No structure required");
	}

	public function PrepareSelectAndJoinByField($table, $prefix, $subfields) {
		// этот метод отвечает только за FIELDS, которые поддтягиваются отдельно в методе hook
		return [];
	}

	public function PrepareSelectAndJoinByFilter($table, $prefix, $subfields) {
		// TODO integrate PrepareSelectAndJoinByFilter, doc
		if (empty($subfields)) {
			return [];
		}

		$select = [];
		$join = [];
		$code = $this->info['CODE'];

		/** @var SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$external_prefix = $prefix . $code . "__";
		$raw_link_code = $external_prefix . $Link->code;

		$join[$raw_link_code] = "LEFT JOIN {$Link->code} AS {$raw_link_code} ON {$prefix}{$table}.ID = {$raw_link_code}.{$this->info['FIELD']}";
		list($add_select, $add_join) = $Link->PrepareSelectAndJoinByFields($subfields, $external_prefix);
		$select += $add_select;
		$join += $add_join;
		return ['SELECT' => $select, 'JOIN' => $join];
	}

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
			'FIELDS' => $subfields ?: ['*'],
		]);
		foreach ($data as $associative) {
			$ID = $associative[$link_key_to_source];
			unset($associative[$link_key_to_source]); // remove looking back identifier
			$elements[$ID][$code][$associative[$Link->key()]] = $associative;
		}

		return $elements;
	}
}