<? if (is_array($RESULT['DATA'][$code])): ?>
<textarea
	class="form-control"
	id="<?= $code ?>"
	name="FIELDS[<?= $code ?>]"
	placeholder=""
	rows="5"
	disabled="disabled"
	data-tooltip="not implemented"
	data-tooltip-placement="top"
><?=print_r($RESULT['DATA'][$code], true)?></textarea>
<? else: ?>
	<input
		type="text"
		class="form-control"
		id="<?= $code ?>"
		name="FIELDS[<?= $code ?>]"
		placeholder=""
		value="<?= $RESULT['DATA'][$code] ?>"
		<?= ($field['DISABLED']) ? 'disabled' : '' ?>
		data-tooltip="not implemented"
		data-tooltip-placement="top"
	>
<? endif; ?>
