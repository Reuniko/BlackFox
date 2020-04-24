<? /** @var \BlackFox\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>

<div
	id="section-settings"
	class="modal fade"
	tabindex="-1"
	role="dialog"
>
	<div class="modal-dialog modal-lg" role="document" style="max-width: 815px; width: 95%;">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h3 class="modal-title"><?= T([
							'en' => 'Section display settings',
							'ru' => 'Настройки отображения секции',
						]) ?></h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<!-- Tab panes -->
					<div class="row">
						<div class="col-sm-6">
							<h4><?= T([
									'en' => 'Filters',
									'ru' => 'Фильтры',
								]) ?></h4>
							<?
							$SELECTED = $RESULT['SETTINGS']['FILTERS'];
							$NAME = 'filters';
							include('section_settings_list.php');
							?>

						</div>
						<div class="col-sm-6">
							<h4><?= T([
									'en' => 'Columns',
									'ru' => 'Колонки',
								]) ?></h4>
							<?
							$SELECTED = $RESULT['SETTINGS']['FIELDS'];
							$NAME = 'fields';
							include('section_settings_list.php');
							?>
						</div>
					</div>


				</div>
				<div class="modal-footer">
					<button
						type="submit"
						class="btn btn-primary"
						data-section-settings-save=""
						name="ACTION"
						value="SaveTableSettings"
					>
						<?= T([
							'en' => 'Save',
							'ru' => 'Сохранить',
						]) ?>
					</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<?= T([
							'en' => 'Close',
							'ru' => 'Закрыть',
						]) ?>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>