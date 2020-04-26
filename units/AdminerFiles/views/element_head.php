<?php /** @var \BlackFox\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
	<h3 class="group_header"><?= T([
			'en' => 'Summary',
			'ru' => 'Сводка',
		]) ?></h3>

	<div class="form-group row">
		<label class="col-sm-3 col-form-label text-right">
			<?= T([
				'en' => 'File',
				'ru' => 'Файл',
			]) ?>
		</label>
		<div class="col-sm-8 col-form-label text-left">
			<a href="<?= $RESULT['DATA']['SRC'] ?>" target="_blank"><?= \BlackFox\Files::I()->GetElementTitle($RESULT['DATA']) ?></a>
			(<?= \BlackFox\Files::I()->GetPrintableFileSize($RESULT['DATA']['SIZE']) ?>)
		</div>
	</div>

<? if (substr($RESULT['DATA']['TYPE'], 0, 5) === 'image'): ?>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label text-right">
			<?= T([
				'en' => 'Preview',
				'ru' => 'Привью',
			]) ?>
		</label>
		<div class="col-sm-8">
			<img
				src="<?= $RESULT['DATA']['SRC'] ?>"
				style="max-height: 300px"
			/>
		</div>
	</div>
<? endif; ?>