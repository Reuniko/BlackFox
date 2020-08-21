<?php
/** @var \BlackFox\Unit $this */
/** @var array $RESULT */
$this->ENGINE->TITLE = T([
	'en' => 'Scheme synchronizer',
	'ru' => 'Синхронизатор схем',
]);
?>

	<form method="post" class="~float-right">
		<a
			class="btn btn-secondary"
			href="?"
		>
			<i class="fa fa-sync"></i>
			<?= T([
				'en' => 'Refresh',
				'ru' => 'Обновить',
			]) ?>
		</a>
		<button
			type="submit"
			name="ACTION"
			value="SynchronizeAll"
			class="btn btn-primary"
		>
			<i class="fa fa-terminal"></i>
			<?= T([
				'en' => 'Synchronize all',
				'ru' => 'Синхронизировать всё',
			]) ?>
		</button>
	</form>

	<hr/>

<? foreach ($RESULT['CORES'] as $namespace => $CORE): ?>
	<h2>
		<i class="fa fa-folder-open"></i>
		<?= $namespace ?>
	</h2>

	<? if (!empty($CORE['ERROR'])): ?>
		<div class="alert alert-danger">
			<?= $CORE['ERROR'] ?>
		</div>
	<? elseif (empty($CORE['DIFFS'])): ?>
		<div class="alert alert-success">
			<i class="fa fa-check"></i>
			<?= T([
				'en' => 'Everything is synchronized',
				'ru' => 'Всё синхронизированно',
			]) ?>
		</div>
	<? else: ?>
		<table class="table table-bordered table-hover bg-white">
			<tr>
				<th>...</th>
				<th>SQL</th>
				<th width="1%"></th>
			</tr>

			<? foreach ($CORE['DIFFS'] as $diff): ?>
				<tr>
					<td>
						<?= $diff['MESSAGE'] ?>
						<? if ($diff['TABLE']): ?>
							<ul class="mb-0">
								<li>
									<?= $diff['TABLE'] ?>
									<? if ($diff['FIELD']): ?>
										<ul class="mb-0">
											<li><?= $diff['FIELD'] ?></li>
										</ul>
									<? endif; ?>
									<? if (!empty($diff['DATA'])): ?>
										<ul>
											<? foreach ($diff['DATA'] as $data): ?>
												<li><?= $data['FIELD'] ?></li>
											<? endforeach; ?>
										</ul>
									<? endif; ?>
								</li>
							</ul>
						<? endif; ?>
					</td>
					<td>
						<pre class="mb-0"><?= $diff['SQL'] ?></pre>
						<? if (!empty($diff['DATA'])): ?>
							<br/>
							<table class="table-bordered">
								<tr>
									<th>Message</th>
									<th>Column</th>
									<th>Reason</th>
									<th>SQL</th>
								</tr>
								<? foreach ($diff['DATA'] as $data): ?>
									<tr>
										<td><?= $data['MESSAGE'] ?></td>
										<td><?= $data['FIELD'] ?></td>
										<td><?= $data['REASON'] ?></td>
										<td>
											<pre class="mb-0"><?= $data['SQL'] ?></pre>
										</td>
									</tr>
								<? endforeach; ?>
							</table>
						<? endif; ?>
					</td>
					<td>
						<? if ($RESULT['MODE'] === 'Compare'): ?>
							<form method="post">
								<input
									type="hidden"
									name="SQL"
									value="<?= $diff['SQL'] ?>"
								/>
								<button
									type="submit"
									name="ACTION"
									value="RunSQL"
									class="btn btn-primary"
								>
									<i class="fa fa-terminal"></i>
								</button>
							</form>
						<? else: ?>

							<? if ($diff['STATUS'] === 'SUCCESS'): ?>
								<div class="alert alert-success">Success</div>
							<? else: ?>
								<div class="alert alert-danger"><?= $diff['ERROR'] ?></div>
							<? endif; ?>

						<? endif; ?>
					</td>
				</tr>
			<? endforeach; ?>
		</table>
	<? endif; ?>
<? endforeach; ?>