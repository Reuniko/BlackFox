<?php

namespace System;

class TypeBool extends Type {
	public static $TYPE = 'BOOL';

	public function FormatOutputValue($element) {
		$value = &$element[$this->info['CODE']];
		if ($value === 'f') {
			$value = false;
		} else {
			$value = (bool)$value;
		}
		return $element;
	}

	public function FormatInputValue($value) {
		return $value ? 1 : 0;
	}

	public function ProvideInfoIntegrity() {
		$this->info['NOT_NULL'] = true;
		$this->info['DEFAULT'] = (bool)($this->info['DEFAULT'] ?: false);
	}

	public function PrintValue($value) {
		echo ($value) ? T([
			'en' => 'Yes',
			'ru' => 'Да',
		]) : T([
			'en' => 'No',
			'ru' => 'Нет',
		]);
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<input
			type="hidden"
			name="<?= $name ?>"
			value="0"
		/>
		<input
			style="margin: 0.4rem 0"
			type="checkbox"
			id="<?= $name ?>"
			name="<?= $name ?>"
			placeholder=""
			value="1"
			<?= ($value) ? 'checked' : '' ?>
			<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
		/>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
		$code = $this->info['CODE'];
		?>
		<select
			class="<?= $class ?>"
			name="<?= $group ?>[<?= $code ?>]"
		>
			<option value=""><?= T([
					'en' => '- do not filter -',
					'ru' => '- не фильтровать -',
				]) ?></option>
			<option value="0" <?= ($filter[$code] === '0') ? 'selected' : '' ?>><?= T([
					'en' => 'No',
					'ru' => 'Нет',
				]) ?></option>
			<option value="1" <?= ($filter[$code] === '1') ? 'selected' : '' ?>><?= T([
					'en' => 'Yes',
					'ru' => 'Да',
				]) ?></option>
		</select>
		<?
	}
}