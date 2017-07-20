<input
	type="hidden"
	name="FIELDS[<?= $code ?>]"
	value="N"
/>
<input
	type="checkbox"
	class=""
	id="<?= $code ?>"
	name="FIELDS[<?= $code ?>]"
	placeholder=""
	value="Y"
	<?= ($RESULT['DATA'][$code] === 'Y') ? 'checked' : '' ?>
	<?= ($field['DISABLED']) ? 'disabled' : '' ?>
/>