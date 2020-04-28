<?php

namespace BlackFox;

class TypePassword extends TypeString {

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<input
			type="password"
			class="<?= $class ?>"
			id="<?= $name ?>"
			name="<?= $name ?>"
			value=""
			<?= ($this->field['DISABLED']) ? 'disabled' : '' ?>
		/>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
	}
}