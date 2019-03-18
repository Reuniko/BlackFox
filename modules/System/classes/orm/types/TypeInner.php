<?php

namespace System;

class TypeInner extends Type {
	public static $TYPE = 'INNER';

	public function ProvideInfoIntegrity() {
		if (empty($this->info['FIELD'])) {
			throw new Exception("Для поля '{$this->info['CODE']}' типа INNER необходимо указать инфо FIELD");
		}
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
		try {
			$link_key_primary = $Link->key();
		} catch (\Exception $error) {
			$link_key_primary = $link_key_to_source;
		}

		if (empty($subfields)) {
			$subfields = [$link_key_primary];
		}
		$subfields[$link_key_to_source] = $link_key_to_source;

		$data = $Link->Select([
			'FILTER' => [$link_key_to_source => $ids],
			'FIELDS' => $subfields,
		]);
		foreach ($data as $associative) {
			$ID = $associative[$link_key_to_source];
			unset($associative[$link_key_to_source]); // remove looking back identifier
			$elements[$ID][$code][$associative[$link_key_primary]] = $associative;
		}

		return $elements;
	}

	public function GenerateJoinAndGroupStatements(SCRUD $Current, $prefix) {
		// debug($this->info, '$this->info');
		/** @var SCRUD $Target */
		$Target = $this->info['LINK']::I();

		$current_alias = $prefix . $Current->code;
		$current_key = $Current->key();
		$target_alias = $prefix . $this->info['CODE'] . '__' . $Target->code;
		$target_key = $this->info['FIELD'];

		$join_statement = "LEFT JOIN {$Target->code} AS {$target_alias} ON {$current_alias}." . $this->Quote($current_key) . " = {$target_alias}." . $this->Quote($target_key);
		$group_statement = "{$Current->code}." . $this->Quote($current_key);

		return [
			'JOIN'  => [$target_alias => $join_statement],
			'GROUP' => [$current_alias => $group_statement],
		];
	}

	public function PrintValue($value) {
		/** @var \System\SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$url = $Link->GetAdminUrl();
		?>
		<ul>
			<? foreach ($value as $row): ?>
				<li>
					<nobr>
						<? if (User::I()->InGroup('root')): ?>
							[<a target="_top" href="<?= $url ?>?ID=<?= $row['ID'] ?>"><?= $row['ID'] ?></a>]
						<? endif; ?>
						<?= $Link->GetElementTitle($row); ?>
					</nobr>
				</li>
			<? endforeach; ?>
		</ul>
		<?
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
	}
}