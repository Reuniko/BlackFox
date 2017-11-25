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
	rows="10"
><?= $RESULT['DATA'][$code] ?></textarea>

<? if ($field['WYSIWYG']): ?>
	<script>CKEDITOR.replace('<?= $code ?>');</script>
<? endif; ?>
