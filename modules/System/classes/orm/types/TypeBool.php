<?php

namespace System;

class TypeBool extends Type {
	public static $TYPE = 'BOOL';

	public function GetStructureStringType() {
		return 'bool';
	}

	public function ProvideInfoIntegrity() {
		$this->info['NOT_NULL'] = true;
	}

	public function PrintValue($value) {
		echo ($value) ? 'Да' : 'Нет';
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
			<option value="">- не фильтровать -</option>
			<option value="0" <?= ($filter[$code] === '0') ? 'selected' : '' ?>>Нет</option>
			<option value="1" <?= ($filter[$code] === '1') ? 'selected' : '' ?>>Да</option>
		</select>
		<?
	}
}