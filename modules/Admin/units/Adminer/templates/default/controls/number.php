<input
	type="number"
	class="form-control"
	id="<?= $code ?>"
	name="FIELDS[<?= $code ?>]"
	placeholder=""
	value="<?= $RESULT['DATA'][$code] ?>"
	<?= ($field['DISABLED']) ? 'disabled' : '' ?>
>