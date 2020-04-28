<?php

namespace BlackFox;

class TypeInner extends Type {

	public $virtual = true;

	public function ProvideInfoIntegrity() {
		if (empty($this->field['INNER_KEY'])) {
			throw new Exception(T([
				'en' => "For field '{$this->field['CODE']}' (of type INNER) you must specify key 'INNER_KEY'",
				'ru' => "Для поля '{$this->field['CODE']}' (тип INNER) необходимо указать ключ 'INNER_KEY'",
			]));
		}
	}

	public function PrepareSelectAndJoinByField($table, $prefix, $subfields) {
		// этот метод отвечает только за FIELDS, которые подтягиваются отдельно в методе HookExternalField
		// this method is only responsible for FIELDS, which are pulled separately in the HookExternalField method
		return [];
	}

	public function HookExternalField($elements, $subfields, $subsort) {
		if (empty($elements)) return $elements;

		$code = $this->field['CODE'];
		$ids = array_keys($elements);

		foreach ($elements as $id => $element) {
			$elements[$id][$code] = [];
		}
		/** @var SCRUD $Link */
		$Link = $this->field['LINK']::I();
		$target_key = $this->field['INNER_KEY'];
		try {
			$link_key_primary = $Link->key();
		} catch (Exception $error) {
			$link_key_primary = $target_key;
		}

		if (empty($subfields)) {
			$subfields = [$link_key_primary];
		}
		$subfields[$target_key] = $target_key;

		$data = $Link->Select([
			'FILTER' => [$target_key => $ids],
			'FIELDS' => $subfields,
			'SORT'   => $subsort,
		]);

		foreach ($data as $associative) {
			$ID = $associative[$target_key];
			unset($associative[$target_key]); // remove looking back identifier
			$elements[$ID][$code][$associative[$link_key_primary]] = $associative;
		}

		return $elements;
	}

	public function GenerateJoinAndGroupStatements(SCRUD $Current, $prefix) {
		/** @var SCRUD $Target */
		$Target = $this->field['LINK']::I();

		$current_alias = $prefix . $Current->code;
		$current_key = $Current->key();
		$target_alias = $prefix . $this->field['CODE'] . '__' . $Target->code;
		$target_key = $this->field['INNER_KEY'];

		$join_statement = "LEFT JOIN {$Target->code} AS {$target_alias} ON {$current_alias}." . $this->DB->Quote($current_key) . " = {$target_alias}." . $this->DB->Quote($target_key);
		$group_statement = "{$Current->code}." . $this->DB->Quote($current_key);

		return [
			'JOIN'  => [$target_alias => $join_statement],
			'GROUP' => [$current_alias => $group_statement],
		];
	}

	public function PrintValue($value) {
		/** @var \BlackFox\SCRUD $Link */
		$Link = $this->field['LINK']::I();
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