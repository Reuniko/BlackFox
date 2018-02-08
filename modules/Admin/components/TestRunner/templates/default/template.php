<?php /** @var \Admin\TestRunner $this */ ?>
<?php /** @var array $RESULT */ ?>
<div class="TestRunner">

	<form method="post">
		<button
			type="submit"
			class="btn btn-primary"
			name="ACTION"
			value="RunAll"
		>
			Run all tests
		</button>
	</form>

	<hr/>

	<?
	$status_to_class = [
		'SUCCESS' => 'alert-success',
		'FAILURE' => 'alert-danger',
	];
	?>

	<? foreach ($RESULT as $test_class_name => $test): ?>
		<div class="card">
			<div class="card-header" title="<?= $test_class_name ?>">
				<?= $test['NAME'] ?>
			</div>
			<? if (!empty($test['RESULTS'])): ?>
				<div class="card-block">
					<? foreach ($test['RESULTS'] as $test_method_name => $test_method_result): ?>
						<? // debug($test_method_result) ?>
						<div class="alert alert-sm <?= $status_to_class[$test_method_result['STATUS']] ?>">
							<strong><?= $test_method_result['NAME'] ?></strong>
							<? if (!empty($test_method_result['ERROR'])): ?>
								<hr/>
								<?= $test_method_result['ERROR']; ?>
							<? endif; ?>
							<? if (!empty($test_method_result['RESULT'])): ?>
								<? debug($test_method_result['RESULT']); ?>
							<? endif; ?>
						</div>
					<? endforeach; ?>
				</div>
			<? endif; ?>
		</div>
	<? endforeach; ?>
</div>
