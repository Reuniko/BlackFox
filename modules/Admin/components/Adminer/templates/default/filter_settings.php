<? /** @var \Admin\Adminer $this */ ?>
<? /** @var array $RESULT */ ?>

<div
	id="filter-settings"
	class="modal fade"
>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Настройки фильтра</h4>
			</div>
			<div class="modal-body">
				<? foreach ($this->SCRUD->structure as $code => $field): ?>
					<div>
						<label class="enum">
							<input
								type="checkbox"
								name=""
							/>
							<span><?= $field['NAME'] ?></span>
						</label>
					</div>
				<? endforeach; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-filter-save="">Сохранить</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>