<?php /** @var \Admin\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<button
	class="btn btn-primary"
	type="submit"
	name="REDIRECT"
	value="Back"
>
	<i class="fa fa-save"></i>
	Сохранить
</button>

<button
	class="btn btn-success"
	type="submit"
	name="REDIRECT"
	value="Stay"
>
	<i class="fa fa-save"></i>
	Применить
</button>

<a
	href="<?= $RESULT['BACK'] ?>"
	class="btn btn-secondary"
>
	<i class="fa fa-ban"></i>
	Отмена
</a>

<button
	class="btn btn-danger float-right"
	type="submit"
	name="ACTION"
	value="Delete"
	data-confirm="Подтвердите удаление"
>
	<i class="fa fa-eraser"></i>
	Удалить
</button>