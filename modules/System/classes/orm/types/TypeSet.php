<?php

namespace System;

class TypeSet extends Type {
	public static $TYPE = 'SET';

	public function GetStructureStringType() {
		return 'set' . '("' . implode('", "', array_keys($this->info['VALUES'])) . '")';
	}

	public function FormatInputValue($values) {
		if (!is_array($values)) {
			$values = [$values];
		}
		foreach ($values as $value) {
			if (!isset($this->info['VALUES'][$value])) {
				throw new ExceptionType("Unknown set value '{$value}' for field '{$this->info['NAME']}'");
			}
		}
		$value = implode(',', $values);
		return $value;
	}

	public function FormatOutputValue($element) {
		$code = $this->info['CODE'];
		if (empty($element[$code])) {
			$element[$code] = [];
		} else {
			$element[$code] = explode(",", $element[$code]);
		}
		$element["$code|VALUES"] = [];
		foreach ($element["$code"] as $key) {
			$element["$code|VALUES"][$key] = $this->info['VALUES'][$key];
		}
		return $element;
	}

	public function PrintValue($value) {
		if (empty($value)) return;
		?>
		<ul class="set">
			<? foreach ($value as $code): ?>
				<li><?= $this->info['VALUES'][$code] ?></li>
			<? endforeach; ?>
		</ul>
		<?
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<input
			type="hidden"
			name="<?= $name ?>"
			value=""
		/>
		<? foreach ($this->info['VALUES'] as $code => $display): ?>
			<div>
				<label class="enum">
					<input
						type="checkbox"
						class="<?= $class ?>"
						name="<?= $name ?>[]"
						value="<?= $code ?>"
						<?= (in_array($code, $value ?: [])) ? 'checked' : '' ?>
						<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
					>
					<span class="dashed"><?= $display ?></span>
				</label>
			</div>
		<? endforeach; ?>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
		$code = $this->info['CODE'];
		?>
		<input
			type="hidden"
			name="<?= $group ?>[<?= $code ?>]"
			value=""
		/>
		<? foreach ($this->info['VALUES'] as $value => $display): ?>
			<div class="col-xs-3">
				<label class="enum">
					<input
						type="checkbox"
						class="<?= $class ?>"
						name="<?= $group ?>[<?= $code ?>][]"
						value="<?= $value ?>"
						<?= (in_array($value, $filter[$code] ?: [])) ? 'checked' : '' ?>
						<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
					>
					<span class="dashed"><?= $display ?></span>
				</label>
			</div>
		<? endforeach; ?>
		<?
	}
}