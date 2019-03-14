<?php

namespace System;

class TypeOuter extends Type {
	public static $TYPE = 'OUTER';

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

		$join = "LEFT JOIN {$Link->code} AS {$raw_link_code} ON {$prefix}{$table}." . $this->Quote($code) . " = {$raw_link_code}." . $this->Quote($Link->key());
		$RESULT = $Link->PrepareSelectAndJoinByFields($subfields, $external_prefix);
		$RESULT['JOIN'] = array_merge([$raw_link_code => $join], $RESULT['JOIN']);
		return $RESULT;
	}

	public function GenerateJoinAndGroupStatements(SCRUD $Current, $prefix) {
		// debug($this->info, '$this->info');
		/** @var SCRUD $Target */
		$Target = $this->info['LINK']::I();

		$current_alias = $prefix . $Current->code;
		$current_key = $this->info['CODE'];
		$target_alias = $prefix . $this->info['CODE'] . '__' . $Target->code;
		$target_key = $Target->key();

		$statement = "LEFT JOIN {$Target->code} AS {$target_alias} ON {$current_alias}." . $this->Quote($current_key) . " = {$target_alias}." . $this->Quote($target_key);
		return [
			'JOIN'  => [$target_alias => $statement],
			'GROUP' => [],
		];
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
		// TODO move to Adminer ?
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
				target="_blank"
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

		if (is_array($IDs) and count($IDs) === 1) {
			$IDs = reset($IDs);
		}

		$elements = !empty($IDs) ? $Link->GetList([
			'FILTER' => ['ID' => $IDs],
			'FIELDS' => ['@'],
		]) : [];

		?>

		<? if (!is_array($IDs)): ?>
			<div
				class="d-flex flex-fill"
				data-outer=""
			>

				<button
					type="button"
					class="btn btn-secondary flex-shrink-1"
					data-outer-multiple=""
					title="Выбрать несколько"
				>
					<i class="fa fa-ellipsis-h"></i>
				</button>

				<div class="flex-grow-1">
					<select
						class="form-control"
						name="<?= $group ?>[<?= $code ?>]"
						data-type="OUTER"
						data-code="<?= $code ?>"
					>
						<? if (!empty($elements)): ?>
							<? $element = reset($elements); ?>
							<option
								value="<?= $element['ID'] ?>"
								selected="selected"
							><?= $Link->GetElementTitle($element) ?></option>
						<? endif; ?>
					</select>
				</div>

				<button
					type="button"
					class="btn btn-secondary flex-shrink-1"
					data-outer-clean=""
					title="Очистить"
				>
					<i class="fa fa-eraser"></i>
				</button>


			</div>
		<? endif; ?>

		<div
			<? if (!is_array($IDs)): ?>
				class="d-none"
			<? endif; ?>
			data-outer-multiple=""
		>
			<select
				class="form-control d-none"
				name="<?= $group ?>[<?= $code ?>][]"
				data-type="OUTER"
				data-code="<?= $code ?>"
				multiple="multiple"
				<? if (!is_array($IDs)): ?>
					disabled="disabled"
				<? endif; ?>
			>
				<? foreach ($elements as $element): ?>
					<option
						value="<?= $element['ID'] ?>"
						selected="selected"
					><?= $Link->GetElementTitle($element) ?></option>
				<? endforeach; ?>
			</select>
		</div>
		<?
	}
}