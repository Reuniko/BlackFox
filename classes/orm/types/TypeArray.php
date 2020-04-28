<?php

namespace BlackFox;

class TypeArray extends TypeText {

	public function FormatInputValue($value) {
		$value = is_array($value) ? $value : [$value];
		$value = json_encode($value, JSON_UNESCAPED_UNICODE);
		return parent::FormatInputValue($value);
	}

	public function FormatOutputValue($element) {
		$code = $this->info['CODE'];
		$element[$code] = json_decode($element[$code], true);
		if (json_last_error()) {
			$element[$code] = null;
		}
		return $element;
	}

	public function PrintValue($value) {
		echo '<pre>';
		print_r($value);
		echo '</pre>';
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<textarea
			class="<?= $class ?>"
			id="<?= $name ?>"
			name="<?= $name ?>"
			disabled="disabled"
			rows="5"
		><? print_r($value) ?></textarea>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {

	}

}