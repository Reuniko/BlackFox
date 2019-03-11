<?php

namespace System;

class TypeString extends Type {
	public static $TYPE = 'STRING';
	const DEFAULT_LENGTH = 255;

	/**
	 * Deleting all extra spaces
	 *
	 * @param string $value
	 * @return string
	 */
	public function FormatInputValue($value) {
		return trim(mb_ereg_replace('#\s+#', ' ', $value));
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<input
			type="text"
			class="<?= $class ?>"
			id="<?= $name ?>"
			name="<?= $name ?>"
			value="<?= $value ?>"
			<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
		/>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
		$code = $this->info['CODE'];
		?>
		<input
			type="text"
			class="<?= $class ?>"
			id="<?= $group ?>[~<?= $code ?>]"
			name="<?= $group ?>[~<?= $code ?>]"
			value="<?= $filter['~' . $code] ?>"
		/>
		<?
	}
}