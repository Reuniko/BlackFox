<? /** @var \Admin\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>

<form method="get" class="form-horizontal">

	<? foreach ($_GET as $code => $value) : ?>
		<? if (!is_array($value)) : ?>
			<input type="hidden" name="<?= $code ?>" value="<?= $value ?>"/>
		<? endif; ?>
	<? endforeach; ?>


	<? if (!empty($RESULT['STRUCTURE']['FILTERS'])): ?>
		<div class="card">
			<div class="card-header">Фильтр</div>
			<div class="card-body" id="filter">
				<? foreach ($RESULT['STRUCTURE']['FILTERS'] as $code => $field): ?>
					<div class="form-group row">
						<label
							class="col-3 col-form-label text-right"
							for="<?= $code ?>"
							title="<?= $code ?>"
						>
							<?= $field['NAME'] ?>
						</label>
						<div class="col-8">
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
					>
						<i class="fa fa-filter"></i>
						Фильтровать
					</button>
					<a
						class="btn btn-secondary"
						href="?"
					>
						<i class="fa fa-ban"></i>
						Отменить
					</a>
				</div>

			</div>
		</div>
	<? endif; ?>
</form>
