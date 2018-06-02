<?php /** @var \Testing\TestRunner $this */ ?>
<?php /** @var array $RESULT */ ?>
<div class="TestRunner">

	<form method="get" class="mb-2">
		<button
			type="submit"
			class="btn btn-primary"
			name="ACTION"
			value="RunAll"
		>
			<i class="fa fa-play"></i>
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
		<div class="card mb-2">
			<div class="card-header">

				<form method="get" class="d-inline">
					<button
						type="submit"
						class="btn btn-sm btn-secondary"
						name="ACTION"
						value="RunOne"
					><i class="fa fa-play"></i></button>
					<input
						type="hidden"
						name="TEST_CLASS_NAME"
						value="<?= $test_class_name ?>"
					/>
				</form>

				<span class="float-right"><?= $test_class_name ?></span>
				<?= $test['NAME'] ?>
			</div>
			<? if (!empty($test['RESULTS'])): ?>
				<div class="card-body">
					<? foreach ($test['RESULTS'] as $test_method_name => $test_method_result): ?>
						<div class="row">
							<div class="col">
								<div class="alert alert-sm alert-info">
									<?= $test_method_result['NAME'] ?>
								</div>
							</div>
							<div class="col">
								<div class="alert alert-sm <?= $status2alert[$test_method_result['STATUS']] ?> limit">
									<i class="<?= $status2icon[$test_method_result['STATUS']] ?>"></i>
									<? if (!empty($test_method_result['RESULT'])): ?>
										<? if (is_array($test_method_result['RESULT'])): ?>
											<pre><?= print_r($test_method_result['RESULT'], true) ?></pre>
										<? else: ?>
											<?= $test_method_result['RESULT']; ?>
										<? endif; ?>
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
