<? /** @var \Admin\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>
<? /** @var array $SELECTED */ ?>
<? /** @var string $NAME */ ?>

<div class="mb-2 text-center">
	<button type="button" class="btn btn-outline-secondary" data-settings-select="settings-<?= $NAME ?>">
		<i class="fa fa-check"></i>
		Выбрать все
	</button>
	<button type="button" class="btn btn-outline-secondary" data-settings-unselect="settings-<?= $NAME ?>">
		<i class="fa fa-times"></i>
		Снять все
	</button>
	<button type="button" class="btn btn-outline-secondary" data-settings-sort="settings-<?= $NAME ?>">
		<i class="fa fa-sort"></i>
		Сортировать
	</button>
</div>

<ul class="sortable" data-connected-sortable="settings-<?= $NAME ?>" id="settings-<?= $NAME ?>">

	<? $unselected = $this->SCRUD->structure; ?>
	<? foreach ($SELECTED as $code): ?>
		<? $field = $this->SCRUD->structure[$code] ?>
		<? unset($unselected[$code]) ?>
		<li data-order="<?= array_search($code, array_keys($this->SCRUD->structure)) ?>">
			<label class="m-0">
				<input
					type="checkbox"
					name="<?= $NAME ?>[]"
					value="<?= $code ?>"
					checked="checked"
				/>
				<span><?= $field['NAME'] ?></span>
			</label>
		</li>
	<? endforeach; ?>
	<? foreach ($unselected as $code => $field): ?>
		<li data-order="<?= array_search($code, array_keys($this->SCRUD->structure)) ?>">
			<label class="m-0">
				<input
					type="checkbox"
					name="<?= $NAME ?>[]"
					value="<?= $code ?>"
				/>
				<span><?= $field['NAME'] ?></span>
			</label>
		</li>
	<? endforeach; ?>

</ul>
