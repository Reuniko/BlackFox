<? /** @var \Admin\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>

<div
	id="section-settings"
	class="modal fade"
>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
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
						<div class="row">
							<div class="col-xs-6">
								<h5>Отображать:</h5>
								<ul class="sortable" data-connected-sortable="settings-filter">
									<? foreach ($this->SCRUD->structure as $code => $field): ?>
										<li>
											<input type="hidden" name="SETTINGS[FILTER][]" value="<?= $code ?>"/>
											<?= $field['NAME'] ?>
										</li>
									<? endforeach; ?>
								</ul>
							</div>
							<div class="col-xs-6">
								<h5>Скрыть:</h5>
								<ul class="sortable" data-connected-sortable="settings-filter">
								</ul>
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane active" id="settings-list">

					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-section-settings-save="">Сохранить</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>