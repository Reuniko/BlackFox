<? /** @var \BlackFox\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>

<form method="get" class="form-horizontal">

	<? foreach ($_GET as $code => $value) : ?>
		<? if (!is_array($value)) : ?>
			<input type="hidden" name="<?= $code ?>" value="<?= $value ?>"/>
		<? endif; ?>
	<? endforeach; ?>


	<? if (!empty($RESULT['STRUCTURE']['FILTERS'])): ?>
		<div class="card">
			<div class="card-header"><?= T([
					'en' => 'Filter',
					'ru' => 'Фильтр',
				]) ?></div>
			<div class="card-body" id="filter">
				<? foreach ($RESULT['STRUCTURE']['FILTERS'] as $code => $field): ?>
					<? if (!isset($this->SCRUD->fields[$code])) continue; ?>
					<div class="form-group row">
						<label
							class="col-sm-3 col-form-label text-sm-right"
							title="<?= $code ?>"
						>
							<?= $field['NAME'] ?>
						</label>
						<div class="col-sm-8">
							<?
							// -------------------------------------------------------------------------------
							$this->SCRUD->Types[$code]->PrintFilterControl($RESULT['FILTER'], 'FILTER');
							// -------------------------------------------------------------------------------
							?>
						</div>
					</div>
				<? endforeach; ?>
				<div class="form-group row mb-0">
					<div class="col-sm-8 offset-sm-3">

						<div class="buttons">
							<button
								class="btn btn-primary"
								type="submit"
							>
								<i class="fa fa-filter"></i>
								<?= T([
									'en' => 'Apply',
									'ru' => 'Фильтровать',
								]) ?>
							</button>
							<a
								class="btn btn-secondary"
								href="?"
							>
								<i class="fa fa-ban"></i>
								<?= T([
									'en' => 'Cancel',
									'ru' => 'Отменить',
								]) ?>
							</a>
						</div>

					</div>
				</div>

			</div>
		</div>
	<? endif; ?>
</form>
