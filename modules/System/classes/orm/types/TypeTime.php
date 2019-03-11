<?php

namespace System;

class TypeTime extends Type {
	public static $TYPE = 'TIME';

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
		<div class="row no-gutters">
			<div class="col-6">
				<input
					type="text"
					class="<?= $class ?>"
					id="<?= $group ?>[><?= $code ?>]"
					name="<?= $group ?>[><?= $code ?>]"
					placeholder="от"
					value="<?= $filter['>' . $code] ?>"
				/>
			</div>
			<div class="col-6">
				<input
					type="text"
					class="<?= $class ?>"
					id="<?= $group ?>[<<?= $code ?>]"
					name="<?= $group ?>[<<?= $code ?>]"
					placeholder="до"
					value="<?= $filter['<' . $code] ?>"
				/>
			</div>
		</div>
		<?
	}
}