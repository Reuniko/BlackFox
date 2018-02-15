<?php /** @var \Admin\Adminer $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>
<?php $this->Debug($this->SCRUD->composition, 'composition'); ?>
<?
if ($RESULT['MODE'] === 'Create') {
	$this->ENGINE->TITLE = "Добавление элемента '{$this->SCRUD->name}'";
} else {
	$this->ENGINE->TITLE = "Редактирование элемента №{$RESULT['DATA']['ID']} '{$this->SCRUD->name}'";
}
?>

<div class="adminer">

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#element" role="tab">Элемент</a>
		</li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div class="tab-pane active tab-element" id="element" role="tabpanel">

			<form method="post" enctype="multipart/form-data" class="form-horizontal">

				<input type="hidden" name="ACTION" value="<?= $RESULT['MODE'] ?>"/>

				<? @include($this->PathInclude('element_head.php')); ?>

				<? foreach ($this->SCRUD->composition as $group_code => $group): ?>
					<? if (!empty($group['FIELDS'])): ?>
						<? if (count($this->SCRUD->composition) > 1): ?>
							<h3 class="group_header" title="<?= $group_code ?>"><?= $group['NAME'] ?></h3>
						<? endif; ?>
					<? endif; ?>
					<? foreach ($group['FIELDS'] as $code => $field): ?>
						<div class="form-group row">
							<label
								class="col-sm-3 col-form-label text-right"
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
						class="btn btn-secondary"
						href="?"
					>
						<i class="fa fa-ban"></i>
						Вернуться
					</a>

					<button
						class="btn btn-danger float-right"
						type="submit"
						name="ACTION"
						value="Delete"
					>
						<i class="fa fa-eraser"></i>
						Удалить
					</button>

				</div>
			</form>


		</div>
	</div>


</div>