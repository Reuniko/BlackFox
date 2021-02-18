<? /** @var \BlackFox\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>
<? /** @var array $SELECTED */ ?>
<? /** @var string $NAME */ ?>

<div class="mb-2 d-flex justify-content-center">
	<button type="button" class="btn btn-outline-secondary text-nowrap" data-settings-select="settings-<?= $NAME ?>">
		<span class="material-icons">done_all</span>
		<span class="d-none d-md-inline-block"><?= T([
				'en' => 'Select all',
				'ru' => 'Выбрать все',
			]) ?></span>
	</button>
	<button type="button" class="btn btn-outline-secondary text-nowrap" data-settings-unselect="settings-<?= $NAME ?>">
		<span class="material-icons">remove_done</span>
		<span class="d-none d-md-inline-block"><?= T([
				'en' => 'Unselect all',
				'ru' => 'Снять все',
			]) ?></span>
	</button>
	<button type="button" class="btn btn-outline-secondary text-nowrap" data-settings-sort="settings-<?= $NAME ?>">
		<span class="material-icons">sort</span>
		<span class="d-none d-md-inline-block"><?= T([
				'en' => 'Sort by default',
				'ru' => 'Сортировать',
			]) ?></span>
	</button>
</div>

<ul class="sortable" data-connected-sortable="settings-<?= $NAME ?>" id="settings-<?= $NAME ?>">

	<? $unselected = $this->SCRUD->fields; ?>
	<? foreach ($SELECTED as $code): ?>
		<? $field = $this->SCRUD->fields[$code] ?>
		<? unset($unselected[$code]) ?>
		<li data-order="<?= array_search($code, array_keys($this->SCRUD->fields)) ?>">
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
		<li data-order="<?= array_search($code, array_keys($this->SCRUD->fields)) ?>">
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
