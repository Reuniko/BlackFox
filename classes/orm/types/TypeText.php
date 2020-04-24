<?php

namespace BlackFox;

class TypeText extends Type {
	public static $TYPE = 'TEXT';

	public function PrintValue($value) {
		if ($this->info['WYSIWYG']) {
			$value = htmlspecialchars_decode($value);
			$value = strip_tags($value);
		}
		if (mb_strlen($value) > 250) {
			$value = mb_substr($value, 0, 250) . '...';
		}
		echo $value;
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<textarea
			class="<?= $class ?>"
			id="<?= $name ?>"
			name="<?= $name ?>"
			<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
			rows="5"
			<? if ($this->info['WYSIWYG']): ?>
				data-wysiwyg=""
				data-wysiwyg-height="300"
			<? endif; ?>
		><?= $value ?></textarea>
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