<?php

namespace System;

class TypeOuter extends Type {
	public static $TYPE = 'OUTER';

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
		$target_alias = $prefix . $this->info['CODE'] . '__' . $Target->code;
		$target_key = $Target->key();

		$statement = "LEFT JOIN {$Target->code} AS {$target_alias} ON {$current_alias}.{$current_key} = {$target_alias}.{$target_key}";
		return [$target_alias => $statement];
	}

	public function PrintValue($value) {
		/** @var \System\SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$url = $Link->GetAdminUrl();
		$ID = is_array($value) ? $value['ID'] : $value;
		?>
		<? if (User::I()->InGroup('root')): ?>
			<nobr>[<a target="_top" href="<?= $url ?>?ID=<?= $ID ?>"><?= $ID ?></a>]</nobr>
		<? endif; ?>
		<?= $Link->GetElementTitle($value); ?>
		<?
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		// TODO move to Adminer
		/** @var \System\SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$code = $this->info['CODE'];
		$ID = is_array($value) ? $value['ID'] : $value;
		if (!is_array($value) and !empty($value)) {
			$value = $Link->Read($value, ['@']);
		}
		?>

		<div
			class="d-flex flex-fill"
			data-outer=""
		>
			<a
				class="btn btn-secondary flex-shrink-1"
				href="<?= $ID ? $Link->GetAdminUrl() . "?ID={$ID}" : 'javascript:void(0);' ?>"
				data-outer-link=""
			>
				<?= $ID ? "№{$ID}" : '...' ?>
			</a>
			<div class="flex-grow-1">
				<select
					class="form-control"
					id="<?= $name ?>"
					name="<?= $name ?>"
					data-link-input="<?= $name ?>"
					<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
					data-type="OUTER"
					data-code="<?= $code ?>"
				>
					<? if (!$this->info['NOT_NULL']): ?>
						<option value=""></option>
					<? endif; ?>

					<? if (!empty($ID)): ?>
						<option
							value="<?= $ID ?>"
							selected="selected"
						><?= $Link->GetElementTitle($value) ?></option>
					<? endif; ?>
				</select>
			</div>
			<? if (!$this->info['DISABLED'] and !$this->info['NOT_NULL']): ?>
				<button
					type="button"
					class="btn btn-secondary flex-shrink-1"
					data-outer-clean=""
				>
					<i class="fa fa-eraser"></i>
				</button>
			<? endif; ?>
		</div>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
		/** @var \System\SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$code = $this->info['CODE'];
		$IDs = $filter[$code];
		$elements = $Link->GetList([
			'FILTER' => ['ID' => $IDs],
			'FIELDS' => ['@'],
		]);
		?>
		<select
			class="form-control"
			name="<?= $group ?>[<?= $code ?>][]"
			data-type="OUTER"
			data-code="<?= $code ?>"
			multiple="multiple"
		>
			<? foreach ($elements as $element): ?>
				<option
					value="<?= $element['ID'] ?>"
					selected="selected"
				><?= $Link->GetElementTitle($element) ?></option>
			<? endforeach; ?>
		</select>
		<?
	}
}