<? /** @var \Admin\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>

<div
	id="section-settings"
	class="modal fade"
>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Настройки отображения секции</h4>
				</div>
				<div class="modal-body">

					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#settings-filter" aria-controls="home" role="tab" data-toggle="tab">
								<i class="glyphicon glyphicon-filter"></i>
								Фильтры
							</a>
						</li>
						<li role="presentation">
							<a href="#settings-list" aria-controls="profile" role="tab" data-toggle="tab">
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
					<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				</div>
			</form>
		</div>
	</div>
</div>