<input
	type="hidden"
	name="FIELDS[<?= $code ?>]"
	value="0"
/>
<input
	type="checkbox"
	class=""
	id="<?= $code ?>"
	name="FIELDS[<?= $code ?>]"
	placeholder=""
	value="1"
	<?= ($RESULT['DATA'][$code]) ? 'checked' : '' ?>
	<?= ($field['DISABLED']) ? 'disabled' : '' ?>
/>