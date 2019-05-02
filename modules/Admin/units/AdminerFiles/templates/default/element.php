<?php /** @var \Admin\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<? if ($RESULT['MODE'] <> 'Create'): ?>
	<? require $this->TemplateParentPath(); ?>
<? else: ?>
	<form method="post" enctype="multipart/form-data">
		<label class="btn btn-secondary btn-file m-0">
			<span><?= T([
					'en' => 'Select file',
					'ru' => 'Выбрать файл',
				]) ?></span>
			<input type="file" name="FIELDS" style="display: none;">
		</label>
		<button
			type="submit"
			name="ACTION"
			value="Create"
			class="btn btn-secondary"
		><?= T([
				'en' => 'Upload',
				'ru' => 'Загрузить',
			]) ?>
		</button>
	</form>

	<script>
        $(document).on('change', ':file', function () {
            var input = $(this),
                // numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            $(this).siblings('span').text(label);
        });
	</script>
<? endif; ?>
