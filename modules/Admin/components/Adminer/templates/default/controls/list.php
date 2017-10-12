<?
/** @var string $code */
/** @var array $field */
/** @var array $RESULT */
/** @var string|array $value */
$value = (array)$value;
?>

<div data-list="<?= $code ?>">

	<div class="input-group" data-template="" style="display: none;">
		<div class="input-group-addon" data-sort="">
			<i class="glyphicon glyphicon-move"></i>
		</div>
		<input
			type="text"
			class="form-control"
			name="FIELDS[<?= $code ?>][]"
			value=""
			<?= ($field['DISABLED']) ? 'disabled' : '' ?>
		>
		<div class="input-group-btn">
			<button class="btn btn-default" type="button" data-delete="">
				<i class="glyphicon glyphicon-trash"></i>
			</button>
		</div>
	</div>

	<? foreach ($value as $key => $element): ?>
		<div class="input-group" data-element="">
			<div class="input-group-addon" data-sort="">
				<i class="glyphicon glyphicon-move"></i>
			</div>
			<input
				type="text"
				class="form-control"
				name="FIELDS[<?= $code ?>][]"
				value="<?= $element ?>"
				<?= ($field['DISABLED']) ? 'disabled' : '' ?>
			>
			<div class="input-group-btn">
				<button class="btn btn-default" type="button" data-delete="">
					<i class="glyphicon glyphicon-trash"></i>
				</button>
			</div>
		</div>
	<? endforeach; ?>
	<button class="btn btn-default" type="button" data-add="">
		<i class="glyphicon glyphicon-plus"></i>
		Добавить
	</button>

</div>