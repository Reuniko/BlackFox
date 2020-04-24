<?php /** @var \BlackFox\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<button
	class="btn btn-primary"
	type="submit"
	name="REDIRECT"
	value="Back"
>
	<i class="fa fa-save"></i>
	<span class="d-none d-md-inline-block"><?= T([
			'en' => 'Save',
			'ru' => 'Сохранить',
		]) ?></span>
</button>

<button
	class="btn btn-success"
	type="submit"
	name="REDIRECT"
	value="Stay"
>
	<i class="fa fa-check"></i>
	<span class="d-none d-md-inline-block"><?= T([
			'en' => 'Apply',
			'ru' => 'Применить',
		]) ?></span>
</button>

<a
	href="<?= $RESULT['BACK'] ?>"
	class="btn btn-secondary"
>
	<i class="fa fa-ban"></i>
	<span class="d-none d-md-inline-block"><?= T([
			'en' => 'Cancel',
			'ru' => 'Отмена',
		]) ?></span>
</a>

<? if ($RESULT['MODE'] === 'Update'): ?>
	<button
		class="btn btn-danger float-right"
		type="submit"
		name="ACTION"
		value="Delete"
		data-confirm="<?= T([
			'en' => 'Confirm deletion',
			'ru' => 'Подтвердите удаление',
		]) ?>"
	>
		<i class="fa fa-trash"></i>
		<span class="d-none d-md-inline-block"><?= T([
				'en' => 'Delete',
				'ru' => 'Удалить',
			]) ?></span>
	</button>
<? endif; ?>
