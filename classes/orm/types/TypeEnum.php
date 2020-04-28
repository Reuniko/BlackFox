<?php

namespace BlackFox;

class TypeEnum extends Type {

	public function FormatInputValue($value) {
		if (!isset($this->info['VALUES'][$value])) {
			throw new ExceptionType("Unknown enum value '{$value}' for field '{$this->info['NAME']}'");
		}
		return $value;
	}

	public function FormatOutputValue($element) {
		$code = $this->info['CODE'];
		$element["$code|VALUE"] = $this->info['VALUES'][$element[$code]];
		return $element;
	}

	public function PrintValue($value) {
		echo $this->info['VALUES'][$value] ?: '';
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<select
			class="<?= $class ?>"
			id="<?= $name ?>"
			name="<?= $name ?>"
			<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
		>
			<? if (!$this->info['NOT_NULL']): ?>
				<option value=""></option>
			<? endif; ?>
			<? foreach ($this->info['VALUES'] as $code => $display): ?>
				<option
					value="<?= $code ?>"
					<?= ((string)$code === (string)$value) ? 'selected' : '' ?>
				><?= $display ?></option>
			<? endforeach; ?>
		</select>
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
			<label class="enum p-2">
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
		<? endforeach; ?>
		<?
	}
}