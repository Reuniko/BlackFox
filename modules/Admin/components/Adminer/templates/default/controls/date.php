<input
	type="text"
	class="form-control"
	id="<?= $code ?>"
	name="FIELDS[<?= $code ?>]"
	placeholder=""
	value="<?= $RESULT['DATA'][$code] ?>"
	<?= ($field['DISABLED']) ? 'disabled' : '' ?>
	data-datepicker=""
>