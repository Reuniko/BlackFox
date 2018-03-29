<select
	class="form-control"
	id="<?= $code ?>"
	name="FIELDS[<?= $code ?>]"
	<?= ($field['DISABLED']) ? 'disabled' : '' ?>
>

	<? if (!$field['NOT_NULL']): ?>
		<option>- choose one -</option>
	<? endif; ?>

	<? foreach ($field['VALUES'] as $value => $display): ?>
		<option
			value="<?= $value ?>"
			<?= ($value === $RESULT['DATA'][$code]) ? 'selected' : '' ?>
		><?= $display ?></option>
	<? endforeach; ?>

</select>