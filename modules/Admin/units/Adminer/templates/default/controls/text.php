<?
/** @var \Admin\Adminer $this */
/** @var string $code */
/** @var array $code */
?>
<textarea
	class="form-control"
	id="<?= $code ?>"
	name="FIELDS[<?= $code ?>]"
	<?= ($field['DISABLED']) ? 'disabled' : '' ?>
	rows="5"
	<? if ($field['WYSIWYG']): ?>
		data-wysiwyg=""
	<? endif; ?>
><?= $RESULT['DATA'][$code] ?></textarea>