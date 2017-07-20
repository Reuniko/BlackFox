<? if ($field['WYSIWYG']): ?>
	</div>
	<div class="col-xs-12" style="margin-top: 10px;">
	<textarea
		class="form-control"
		id="<?= $code ?>"
		name="FIELDS[<?= $code ?>]"
		<?= ($field['DISABLED']) ? 'disabled' : '' ?>
		rows="10"
	><?= $RESULT['DATA'][$code] ?></textarea>
	<script>CKEDITOR.replace('<?= $code ?>');</script>
<? else: ?>
	<textarea
		class="form-control"
		id="<?= $code ?>"
		name="FIELDS[<?= $code ?>]"
		<?= ($field['DISABLED']) ? 'disabled' : '' ?>
		rows="10"
	><?= $RESULT['DATA'][$code] ?></textarea>
<? endif; ?>
