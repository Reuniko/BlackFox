<?php /** @var \Admin\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<h3 class="group_header">Сводка</h3>

<div class="form-group row">
	<label class="col-sm-3 col-form-label text-right">
		Файл
	</label>
	<div class="col-sm-8">
		<p class="form-control-static">
			<a href="<?= $RESULT['DATA']['SRC'] ?>" target="_blank"><?= $RESULT['DATA']['NAME'] ?></a>
			(<?= ceil($RESULT['DATA']['SIZE'] / 1024 / 1024 * 100) / 100 ?> мегабайт)
		</p>
	</div>
</div>

<? if (substr($RESULT['DATA']['TYPE'], 0, 5) === 'image'): ?>
<div class="form-group row">
	<label class="col-sm-3 col-form-label text-right">
		Привью
	</label>
	<div class="col-sm-8">
		<img
			src="<?= $RESULT['DATA']['SRC'] ?>"
			style="max-height: 300px"
		/>
	</div>
</div>
<? endif; ?>