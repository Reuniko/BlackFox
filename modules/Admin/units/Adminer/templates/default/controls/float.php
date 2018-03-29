<input
	type="number"
	step="any"
	class="form-control"
	id="<?= $code ?>"
	name="FIELDS[<?= $code ?>]"
	placeholder=""
	value="<?= $RESULT['DATA'][$code] ?>"
	<?= ($field['DISABLED']) ? 'disabled' : '' ?>
>