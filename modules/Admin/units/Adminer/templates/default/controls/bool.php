<input
	type="hidden"
	name="FIELDS[<?= $code ?>]"
	value="0"
/>
<input
	style="margin: 0.4rem 0"
	type="checkbox"
	id="<?= $code ?>"
	name="FIELDS[<?= $code ?>]"
	placeholder=""
	value="1"
	<?= ($RESULT['DATA'][$code]) ? 'checked' : '' ?>
	<?= ($field['DISABLED']) ? 'disabled' : '' ?>
/>
