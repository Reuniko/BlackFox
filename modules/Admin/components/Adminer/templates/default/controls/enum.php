<select
	class="form-control"
	id="<?= $code ?>"
	name="FIELDS[<?= $code ?>]"
	<?= ($field['DISABLED']) ? 'disabled' : '' ?>
>
	<option>- choose one -</option>
	<? foreach ($field['VALUES'] as $value => $display): ?>
		<option
			value="<?= $value ?>"
			<?= ($value === $RESULT['DATA'][$code]) ? 'selected' : '' ?>
		><?= $display ?></option>
	<? endforeach; ?>
</select>