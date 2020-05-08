<?php /** @var \BlackFox\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>

<? if (!empty($RESULT['ACTIONS'])): ?>
	<div class="actions mb-2 text-right">
		<? foreach ($RESULT['ACTIONS'] as $action_id => $action): ?>
			<button
				type="button"
				class="btn btn-info"
				data-toggle="modal"
				data-target="#action_<?= $action_id ?>"
			>
				<? if ($action['ICON']): ?>
					<i class="<?= $action['ICON'] ?>"></i>
				<? endif; ?>
				<?= $action['NAME'] ?>
			</button>
		<? endforeach; ?>
	</div>

	<? foreach ($RESULT['ACTIONS'] as $action_id => $action): ?>
		<div class="modal" id="action_<?= $action_id ?>" tabindex="-1" role="dialog">
			<div class="modal-dialog ~modal-dialog-centered" role="document">
				<form method="post">
					<input
						type="hidden"
						name="ACTION"
						value="ExecuteAction"
					/>
					<input
						type="hidden"
						name="ACTION_ID"
						value="<?= $action_id ?>"
					/>
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><?= $action['NAME'] ?></h5>
						</div>
						<div class="modal-body">
							<? if ($action['DESCRIPTION']): ?>
								<p><?= $action['DESCRIPTION'] ?></p>
							<? endif; ?>

							<? if (!empty($action['PARAMS'])): ?>
								<? foreach ($action['PARAMS'] as $param_id => $param): ?>
									<div class="form-group">
										<label class="col-form-label"><?= $param['NAME'] ?>:</label>
										<?
										\BlackFox\FactoryType::Get($param)->PrintFormControl(null, "ACTION_PARAMS[{$param_id}]");
										?>
									</div>
								<? endforeach; ?>
							<? endif; ?>

						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary">
								<?= T([
									'en' => 'Execute',
									'ru' => 'Выполнить',
								]) ?>
							</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">
								<?= T([
									'en' => 'Cancel',
									'ru' => 'Отмена',
								]) ?>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	<? endforeach; ?>
<? endif; ?>