<?php

namespace System;

class TypePassword extends TypeString {
	public static $TYPE = 'PASSWORD';

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<input
			type="password"
			class="<?= $class ?>"
			id="<?= $name ?>"
			name="<?= $name ?>"
			value=""
			<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
		/>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
	}
}