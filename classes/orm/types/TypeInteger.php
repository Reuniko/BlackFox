<?php

namespace BlackFox;

class TypeInteger extends Type {

	public $db_type = 'int';

	public function FormatInputValue($value) {
		if (!is_numeric($value)) {
			throw new ExceptionType(T([
			    'en' => "Expected numerical value for '{$this->field['CODE']}', received: '{$value}'",
			    'ru' => "Ожидалось числовое значение для '{$this->field['CODE']}', получено: '{$value}'",
			]));
		}
		return (int)$value;
	}

	public function FormatOutputValue($element) {
		return $element;
		// TODO convert to integer (if not null)
		// $element[$this->info['CODE']] = (int)$element[$this->info['CODE']];
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<input
			type="number"
			class="<?= $class ?>"
			id="<?= $name ?>"
			name="<?= $name ?>"
			placeholder=""
			value="<?= $value ?>"
			<?= ($this->field['DISABLED']) ? 'disabled' : '' ?>
		>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
		$code = $this->field['CODE'];
		?>
		<div class="row no-gutters">
			<div class="col-6">
				<input
					type="number"
					step="1"
					class="<?= $class ?>"
					id="<?= $group ?>[><?= $code ?>]"
					name="<?= $group ?>[><?= $code ?>]"
					placeholder="<?= T([
						'en' => 'from',
						'ru' => 'от',
					]) ?>"
					value="<?= $filter['>' . $code] ?>"
				>
			</div>
			<div class="col-6">
				<input
					type="number"
					step="1"
					class="<?= $class ?>"
					id="<?= $group ?>[<<?= $code ?>]"
					name="<?= $group ?>[<<?= $code ?>]"
					placeholder="<?= T([
						'en' => 'to',
						'ru' => 'до',
					]) ?>"
					value="<?= $filter['<' . $code] ?>"
				>
			</div>
		</div>
		<?
	}
}