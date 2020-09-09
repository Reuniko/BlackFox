<?php /** @var \BlackFox\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<? if ($RESULT['MODE'] <> 'Create'): ?>
	<? require $this->GetParentView(); ?>
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

	<?php
	$this->ENGINE->AddHeaderScript(
		$this->ENGINE->GetRelativePath(
			__DIR__ . '/on_file_change.js'
		)
	);
	?>
<? endif; ?>
