<?php /** @var \Admin\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<button
	class="btn btn-primary"
	type="submit"
	name="REDIRECT"
	value="Back"
>
	<i class="fa fa-save"></i>
	<span class="d-none d-md-inline-block">Сохранить</span>
</button>

<button
	class="btn btn-success"
	type="submit"
	name="REDIRECT"
	value="Stay"
>
	<i class="fa fa-check"></i>
	<span class="d-none d-md-inline-block">Применить</span>
</button>

<a
	href="<?= $RESULT['BACK'] ?>"
	class="btn btn-secondary"
>
	<i class="fa fa-ban"></i>
	<span class="d-none d-md-inline-block">Отмена</span>
</a>

<? if ($RESULT['MODE'] === 'Update'): ?>
	<button
		class="btn btn-danger float-right"
		type="submit"
		name="ACTION"
		value="Delete"
		data-confirm="Подтвердите удаление"
	>
		<i class="fa fa-trash"></i>
		<span class="d-none d-md-inline-block">Удалить</span>
	</button>
<? endif; ?>
