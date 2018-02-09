<?php /** @var \Admin\TestRunner $this */ ?>
<?php /** @var array $RESULT */ ?>
<div class="TestRunner">

	<form method="post" class="mb-2">
		<button
			type="submit"
			class="btn btn-primary"
			name="ACTION"
			value="RunAll"
		>
			Запустить все тесты
		</button>
	</form>

	<?
	$status2alert = [
		'SUCCESS' => 'alert-success',
		'FAILURE' => 'alert-danger',
	];
	$status2icon = [
		'SUCCESS' => 'fa fa-check',
		'FAILURE' => 'fa fa-times',
	];
	?>

	<? foreach ($RESULT as $test_class_name => $test): ?>
		<div class="card">
			<div class="card-header">
				<span class="float-right"><?= $test_class_name ?></span>
				<?= $test['NAME'] ?>
			</div>
			<? if (!empty($test['RESULTS'])): ?>
				<div class="card-block">
					<? foreach ($test['RESULTS'] as $test_method_name => $test_method_result): ?>
						<div class="row">
							<div class="col">
								<div class="alert alert-sm alert-info">
									<?= $test_method_result['NAME'] ?>
								</div>
							</div>
							<div class="col">
								<div class="alert alert-sm <?= $status2alert[$test_method_result['STATUS']] ?>">
									<i class="<?= $status2icon[$test_method_result['STATUS']] ?>"></i>
									<? if (!empty($test_method_result['ERROR'])): ?>
										<?= $test_method_result['ERROR']; ?>
									<? elseif (!empty($test_method_result['RESULT'])): ?>
										<?= $test_method_result['RESULT'] ?>
									<? else: ?>
									<? endif; ?>
								</div>
							</div>
						</div>
					<? endforeach; ?>
				</div>
			<? endif; ?>
		</div>
	<? endforeach; ?>
</div>
