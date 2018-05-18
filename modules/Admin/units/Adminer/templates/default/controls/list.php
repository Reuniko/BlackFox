<?
/** @var string $code */
/** @var array $field */
/** @var array $RESULT */
/** @var string|array $value */
$value = (array)$value;
?>

<div data-list="<?= $code ?>">

	<input
		type="hidden"
		name="FIELDS[<?= $code ?>]"
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
			class="form-control"
			name="FIELDS[<?= $code ?>][]"
			value=""
			disabled="disabled"
			<?= ($field['DISABLED']) ? 'disabled' : '' ?>
		>
		<div class="input-group-append">
			<button class="btn btn-secondary" type="button" data-delete="">
				<i class="fa fa-trash"></i>
			</button>
		</div>
	</div>

	<? foreach ($value as $key => $element): ?>
		<div class="input-group" data-element="">
			<div class="input-group-prepend" data-sort="">
				<span class="input-group-text">
					<i class="fa fa-arrows-alt"></i>
				</span>
			</div>
			<input
				type="text"
				class="form-control"
				name="FIELDS[<?= $code ?>][]"
				value="<?= $element ?>"
				<?= ($field['DISABLED']) ? 'disabled' : '' ?>
			>
			<div class="input-group-append">
				<button class="btn btn-secondary" type="button" data-delete="">
					<i class="fa fa-trash"></i>
				</button>
			</div>
		</div>
	<? endforeach; ?>

</div>