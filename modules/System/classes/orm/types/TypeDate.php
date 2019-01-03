<?php

namespace System;

class TypeDate extends Type {
	public static $name = 'Date';
	public static $code = 'DATE';

	public function GetStructureStringType() {
		return 'date';
	}

	public function FormatInputValue($value) {
		$value = is_numeric($value) ? $value : strtotime($value);
		$value = date('Y-m-d', $value);
		return $value;
	}

	public function FormatOutputValue($element) {
		$code = $this->info['CODE'];
		$element[$code . '|TIMESTAMP'] = strtotime($element[$code]);
		return $element;
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<input
			type="text"
			class="<?= $class ?>"
			id="<?= $name ?>"
			name="<?= $name ?>"
			placeholder=""
			value="<?= $value ?>"
			<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
			data-datepicker=""
		/>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
		$code = $this->info['CODE'];
		?>
		<div class="row no-gutters">
			<div class="col-6">
				<input
					type="text"
					class="<?= $class ?>"
					id="<?= $code ?>"
					name="<?= $group ?>[><?= $code ?>]"
					placeholder="от"
					value="<?= $filter['>' . $code] ?>"
					data-datepicker=""
				/>
			</div>
			<div class="col-6">
				<input
					type="text"
					class="<?= $class ?>"
					id="<?= $code ?>"
					name="<?= $group ?>[<<?= $code ?>]"
					placeholder="до"
					value="<?= $filter['<' . $code] ?>"
					data-datepicker=""
				/>
			</div>
		</div>
		<?
	}
}
