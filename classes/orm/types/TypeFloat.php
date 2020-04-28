<?php

namespace BlackFox;

class TypeFloat extends Type {

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
					step="any"
					class="<?= $class ?>"
					id="<?= $code ?>"
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