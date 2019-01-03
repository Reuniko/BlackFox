<?php

namespace System;

class TypeList extends TypeText {
	public static $name = 'List';
	public static $code = 'LIST';

	public function FormatInputValue($value) {
		$value = is_array($value) ? $value : [$value];
		$value = array_filter($value, 'strlen');
		$value = json_encode($value, JSON_UNESCAPED_UNICODE);
		return parent::FormatInputValue($value);
	}

	public function FormatOutputValue($element) {
		$code = $this->info['CODE'];
		$element[$code] = json_decode($element[$code], true);
		if (json_last_error()) {
			$element[$code] = [];
		}
		return $element;
	}

	public function PrintValue($value) {
		if (empty($value)) return;
		?>
		<ul class="list">
			<? foreach ($value as $element): ?>
				<li><?= $element ?></li>
			<? endforeach; ?>
		</ul>
		<?
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		$value = (array)$value;
		?>

		<div data-list="<?= $name ?>">

			<input
				type="hidden"
				name="<?= $name ?>"
				value=""
			/>

			<button class="btn btn-secondary" type="button" data-add="">
				<i class="fa fa-plus"></i>
				Добавить
			</button>

			<div class="input-group" data-template="" style="display: none;">
				<div class="input-group-prepend" data-sort="">
					<span class="input-group-text">
						<i class="fa fa-arrows-alt"></i>
					</span>
				</div>
				<input
					type="text"
					class="<?= $class ?>"
					name="<?= $name ?>[]"
					value=""
					disabled="disabled"
				>
				<div class="input-group-append">
					<button class="btn btn-secondary" type="button" data-delete="">
						<i class="fa fa-trash"></i>
					</button>
				</div>
			</div>

			<? foreach ($value as $element): ?>
				<div class="input-group" data-element="">
					<div class="input-group-prepend" data-sort="">
						<span class="input-group-text">
							<i class="fa fa-arrows-alt"></i>
						</span>
					</div>
					<input
						type="text"
						class="<?= $class ?>"
						name="<?= $name ?>[]"
						value="<?= $element ?>"
						<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
					>
					<div class="input-group-append">
						<button class="btn btn-secondary" type="button" data-delete="">
							<i class="fa fa-trash"></i>
						</button>
					</div>
				</div>
			<? endforeach; ?>

		</div>
		<?
	}

}