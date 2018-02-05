<? /** @var \Admin\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>

<div
	id="section-settings"
	class="modal fade"
	tabindex="-1"
	role="dialog"
>
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h4 class="modal-title">Настройки отображения секции</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<ul class="nav nav-tabs">
						<li class="nav-item">
							<a class="nav-link active" href="#settings-filter" aria-controls="home" role="tab" data-toggle="tab">
								<i class="glyphicon glyphicon-filter"></i>
								Фильтры
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#settings-list" aria-controls="profile" role="tab" data-toggle="tab">
								<i class="glyphicon glyphicon-th-list"></i>
								Колонки списка
							</a>
						</li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="settings-filter">
							<h4>Порядок и отображение фильтров</h4>
							<ul class="sortable" data-connected-sortable="settings-filter">
								<?
								$SELECTED = $RESULT['SETTINGS']['FILTERS'];
								$NAME = 'FILTERS';
								include('section_settings_list.php');
								?>
							</ul>
						</div>
						<div role="tabpanel" class="tab-pane" id="settings-list">
							<h4>Порядок и отображение колонок списка</h4>
							<ul class="sortable" data-connected-sortable="settings-list">
								<?
								$SELECTED = $RESULT['SETTINGS']['FIELDS'];
								$NAME = 'FIELDS';
								include('section_settings_list.php');
								?>
							</ul>
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