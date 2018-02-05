<? /** @var \Admin\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>

<div
	id="section-settings"
	class="modal fade"
	tabindex="-1"
	role="dialog"
>
	<div class="modal-dialog modal-lg" role="document" style="min-width: 893px;">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h3 class="modal-title">Настройки отображения секции</h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<!-- Tab panes -->
					<div class="row">
						<div class="col">
							<h4>Фильтры</h4>
							<?
							$SELECTED = $RESULT['SETTINGS']['FILTERS'];
							$NAME = 'filters';
							include('section_settings_list.php');
							?>

						</div>
						<div class="col">
							<h4>Колонки</h4>
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
					>Сохранить
					</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				</div>
			</form>
		</div>
	</div>
</div>