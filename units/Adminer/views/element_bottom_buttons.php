<?php /** @var \BlackFox\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<button
	class="btn btn-primary"
	type="submit"
	name="REDIRECT"
	value="<?= $RESULT['BACK'] ?>"
>
	<span class="material-icons">save</span>
	<span class="d-none d-md-inline-block"><?= T([
			'en' => 'Save',
			'ru' => 'Сохранить',
		]) ?></span>
</button>

<button
	class="btn btn-success"
	type="submit"
	name="REDIRECT"
	value=""
>
	<span class="material-icons">save_alt</span>
	<span class="d-none d-md-inline-block"><?= T([
			'en' => 'Apply',
			'ru' => 'Применить',
		]) ?></span>
</button>

<a
	href="<?= $RESULT['BACK'] ?>"
	class="btn btn-secondary"
>
	<span class="material-icons">block</span>
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
		<span class="material-icons">delete_outline</span>
		<span class="d-none d-md-inline-block"><?= T([
				'en' => 'Delete',
				'ru' => 'Удалить',
			]) ?></span>
	</button>
<? endif; ?>