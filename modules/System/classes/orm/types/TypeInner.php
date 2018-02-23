<?php

namespace System;

class TypeInner extends Type {
	public static $name = 'Inner';
	public static $code = 'INNER';

	public function GetStructureStringType() {
		throw new ExceptionType("No structure required");
	}

	public function PrepareSelectAndJoinByField($table, $prefix, $subfields) {
		return [];
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