<? /** @var \BlackFox\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>
<? /** @var array $SELECTED */ ?>
<? /** @var string $NAME */ ?>

<div class="mb-2 text-center">
	<button type="button" class="btn btn-outline-secondary" data-settings-select="settings-<?= $NAME ?>">
		<i class="fa fa-check"></i>
		<span class="d-none d-md-inline-block"><?= T([
				'en' => 'Select all',
				'ru' => 'Выбрать все',
			]) ?></span>
	</button>
	<button type="button" class="btn btn-outline-secondary" data-settings-unselect="settings-<?= $NAME ?>">
		<i class="fa fa-times"></i>
		<span class="d-none d-md-inline-block"><?= T([
				'en' => 'Unselect all',
				'ru' => 'Снять все',
			]) ?></span>
	</button>
	<button type="button" class="btn btn-outline-secondary" data-settings-sort="settings-<?= $NAME ?>">
		<i class="fa fa-sort"></i>
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
