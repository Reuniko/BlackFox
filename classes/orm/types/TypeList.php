<?php

namespace BlackFox;

class TypeList extends TypeText {

	public function FormatInputValue($value) {
		$value = is_array($value) ? $value : [$value];
		$value = array_filter($value, 'strlen');
		$value = json_encode($value, JSON_UNESCAPED_UNICODE);
		return parent::FormatInputValue($value);
	}

	public function FormatOutputValue($element) {
		$code = $this->field['CODE'];
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
				<span class="material-icons">add</span>
				Добавить
			</button>

			<div class="input-group" data-template="" style="display: none;">
				<div class="input-group-prepend" data-sort="">
					<span class="input-group-text">
						<span class="material-icons">open_with</span>
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
						<span class="material-icons">delete</span>
					</button>
				</div>
			</div>

			<? foreach ($value as $element): ?>
				<div class="input-group" data-element="">
					<div class="input-group-prepend" data-sort="">
						<span class="input-group-text">
							<span class="material-icons">open_with</span>
						</span>
					</div>
					<input
						type="text"
						class="<?= $class ?>"
						name="<?= $name ?>[]"
						value="<?= $element ?>"
						<?= ($this->field['DISABLED']) ? 'disabled' : '' ?>
					>
					<div class="input-group-append">
						<button class="btn btn-secondary" type="button" data-delete="">
							<span class="material-icons">delete</span>
						</button>
					</div>
				</div>
			<? endforeach; ?>

		</div>
		<?
	}

}