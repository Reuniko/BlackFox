<?php
/**@var BlackFox\Engine $this */
$this->TITLE = T([
	'en' => 'SQL console',
	'ru' => 'SQL консоль',
]);
?>
<form method="post">
	<div class="form-group">
		<textarea
			class="form-control"
			name="SQL"
			rows="5"
		><?= htmlspecialchars($_REQUEST['SQL']) ?></textarea>
	</div>
	<input
		type="submit"
		value="<?= T([
			'en' => 'Execute',
			'ru' => 'Выполнить',
		]) ?>"
		class="btn btn-success"
	/>
</form>

<?php
if (!empty($_REQUEST['SQL'])) {
	echo '<hr/>';
	try {
		$data = $this->Database->Query($_REQUEST['SQL']);
	} catch (\BlackFox\ExceptionSQL $error) {
		echo "<div class='alert alert-danger'>{$error->getMessage()}</pre></div>";
	}
}
?>
<? if (!empty($data)): ?>
	<table class="table table-bordered table-hover bg-white">
		<tr>
			<? foreach (reset($data) as $column => $trash_value): ?>
				<th><?= $column ?></th>
			<? endforeach; ?>
		</tr>
		<? foreach ($data as $row): ?>
			<tr>
				<? foreach ($row as $column => $value): ?>
					<td><?= htmlspecialchars($value) ?></td>
				<? endforeach; ?>
			</tr>
		<? endforeach; ?>
	</table>
<? endif; ?>
