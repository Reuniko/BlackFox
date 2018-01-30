<?php /** @var \Admin\Adminer $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>
<?php $this->Debug($this->SCRUD->composition, 'composition'); ?>

<h2>
	<? if ($RESULT['MODE'] === 'Create'): ?>
		Добавление элемента
	<? else: ?>
		Редактирование элемента №<?= $RESULT['DATA']['ID'] ?>
	<? endif; ?>
</h2>

<form method="post" enctype="multipart/form-data" class="form-horizontal adminer">

	<input type="hidden" name="ACTION" value="<?= $RESULT['MODE'] ?>"/>

	<? @include($this->PathInclude('element_head.php')); ?>

	<? foreach ($this->SCRUD->composition as $group_code => $group): ?>
		<h3 class="group_header"><?= $group['NAME'] ?: "{{$group_code}}" ?></h3>
		<? foreach ($group['FIELDS'] as $code => $field): ?>
			<div class="form-group">
				<label
					class="col-sm-3 control-label"
					for="<?= $code ?>"
					title="<?= $code ?>"
				>
					<?= $field['NAME'] ?: "{{$code}}" ?>
				</label>
				<div class="col-sm-8">
					<?
					$value = $RESULT['DATA'][$code];
					$inc = strtolower($field['VIEW']) ?: strtolower($field['TYPE']);

					try {
						require($this->Path('controls/' . $inc . '.php'));
					} catch (\Exception $error) {
						require($this->Path('controls/' . '_default' . '.php'));
					}
					?>
				</div>
			</div>
		<? endforeach; ?>
	<? endforeach; ?>

	<? @include($this->PathInclude('element_foot.php')); ?>

	<br/>

	<div class="buttons">


		<button
			class="btn btn-primary"
			type="submit"
			name="REDIRECT"
			value="Return"
		>
			<i class="glyphicon glyphicon-ok"></i>
			Сохранить
		</button>

		<button
			class="btn btn-success"
			type="submit"
			name="REDIRECT"
			value="Stay"
		>
			<i class="glyphicon glyphicon-ok"></i>
			Применить
		</button>

		<a
			class="btn btn-default"
			href="?"
		>
			<i class="glyphicon glyphicon-ban-circle"></i>
			Вернуться
		</a>

		<button
			class="btn btn-danger pull-right"
			type="submit"
			name="ACTION"
			value="Delete"
		>
			<i class="glyphicon glyphicon-remove"></i>
			Удалить
		</button>

	</div>
</form>