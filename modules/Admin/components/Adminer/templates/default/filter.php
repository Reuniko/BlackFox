<? /** @var \Admin\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>

<? require($this->Path('filter_settings.php')); ?>


<form method="get" class="form-horizontal">

	<? foreach ($_GET as $code => $value) : ?>
		<? if (!is_array($value)) : ?>
			<input type="hidden" name="<?= $code ?>" value="<?= $value ?>"/>
		<? endif; ?>
	<? endforeach; ?>


	<div class="panel panel-default">
		<div class="panel-heading">
			<a
				data-filter-settings=""
				class="btn btn-default pull-right"
				style="margin: -7px;"
				data-toggle="modal"
				data-target="#filter-settings"
			>
				<i class="glyphicon glyphicon-th-list"></i>
			</a>
			Фильтр
		</div>
		<div class="panel-body">
			<? foreach ($this->SCRUD->structure as $code => $field): ?>
				<div class="form-group">
					<label
						class="col-sm-3 control-label"
						for="<?= $code ?>"
						title="<?= $code ?>"
					>
						<?= $field['NAME'] ?>
					</label>
					<div class="col-sm-8">
						<?
						try {
							require($this->Path('filters/' . strtolower($field['TYPE']) . '.php'));
						} catch (\Exception $error) {
							require($this->Path('filters/' . '_default' . '.php'));
						}
						?>
					</div>
				</div>
			<? endforeach; ?>

			<div class="buttons">
				<button
					class="btn btn-primary"
					type="submit"
					name=""
					value=""
				>
					<i class="glyphicon glyphicon-filter"></i>
					Фильтровать
				</button>
				<a
					class="btn btn-default"
					href="?"
				>
					<i class="glyphicon glyphicon-ban-circle"></i>
					Отменить
				</a>
			</div>

		</div>
	</div>
</form>