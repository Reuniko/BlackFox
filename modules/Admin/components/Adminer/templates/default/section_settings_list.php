<? /** @var \Admin\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>
<? /** @var array $SELECTED */ ?>
<? /** @var string $NAME */ ?>

<? $unselected = $this->SCRUD->structure; ?>
<? foreach ($SELECTED as $code): ?>
	<? $field = $this->SCRUD->structure[$code] ?>
	<? unset($unselected[$code]) ?>
	<li>
		<label class="enum">
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
	<li>
		<label class="enum">
			<input
				type="checkbox"
				name="<?= $NAME ?>[]"
				value="<?= $code ?>"
			/>
			<span><?= $field['NAME'] ?></span>
		</label>
	</li>
<? endforeach; ?>