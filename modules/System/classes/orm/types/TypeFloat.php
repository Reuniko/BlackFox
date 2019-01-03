<?php

namespace System;

class TypeFloat extends Type {
	public static $name = 'Float';
	public static $code = 'FLOAT';

	public function GetStructureStringType() {
		$length = $this->info['LENGTH'] ?: 13;
		$decimals = $this->info['DECIMALS'] ?: 2;
		return "float({$length},{$decimals})";
	}

	public function FormatInputValue($value) {
		return str_replace(',', '.', (float)$value);
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<input
			type="number"
			step="any"
			class="<?= $class ?>"
			id="<?= $name ?>"
			name="<?= $name ?>"
			value="<?= $value ?>"
			<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
		>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
		$code = $this->info['CODE'];
		?>
		<div class="row no-gutters">
			<div class="col-6">
				<input
					type="number"
					step="any"
					class="<?= $class ?>"
					id="<?= $code ?>"
					name="<?= $group ?>[><?= $code ?>]"
					placeholder="от"
					value="<?= $filter['>' . $code] ?>"
				>
			</div>
			<div class="col-6">
				<input
					type="number"
					step="any"
					class="<?= $class ?>"
					id="<?= $code ?>"
					name="<?= $group ?>[<<?= $code ?>]"
					placeholder="до"
					value="<?= $filter['<' . $code] ?>"
				>
			</div>
		</div>
		<?
	}
}