<?php

namespace BlackFox;

class TypeEnum extends Type {

	public $db_type = 'enum';

	public function FormatInputValue($value) {
		if (!isset($this->field['VALUES'][$value])) {
			throw new ExceptionType("Unknown enum value '{$value}' for field '{$this->field['NAME']}'");
		}
		return $value;
	}

	public function FormatOutputValue($element) {
		$code = $this->field['CODE'];
		$element["$code|VALUE"] = $this->field['VALUES'][$element[$code]];
		return $element;
	}

	public function PrintValue($value) {
		echo $this->field['VALUES'][$value] ?: '';
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<select
			class="<?= $class ?>"
			id="<?= $name ?>"
			name="<?= $name ?>"
			<?= ($this->field['DISABLED']) ? 'disabled' : '' ?>
		>
			<? if (!$this->field['NOT_NULL']): ?>
				<option value=""></option>
			<? endif; ?>
			<? foreach ($this->field['VALUES'] as $code => $display): ?>
				<option
					value="<?= $code ?>"
					<?= ((string)$code === (string)$value) ? 'selected' : '' ?>
				><?= $display ?></option>
			<? endforeach; ?>
		</select>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
		$code = $this->field['CODE'];
		?>
		<input
			type="hidden"
			name="<?= $group ?>[<?= $code ?>]"
			value=""
		/>
		<? foreach ($this->field['VALUES'] as $value => $display): ?>
			<label class="enum">
				<input
					type="checkbox"
					class="<?= $class ?>"
					name="<?= $group ?>[<?= $code ?>][]"
					value="<?= $value ?>"
					<?= (in_array($value, $filter[$code] ?: [])) ? 'checked' : '' ?>
					<?= ($this->field['DISABLED']) ? 'disabled' : '' ?>
				>
				<span class="dashed"><?= $display ?></span>
			</label>
		<? endforeach; ?>
		<?
	}
}