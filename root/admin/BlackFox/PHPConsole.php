<?php
/**@var BlackFox\Engine $this */
$this->TITLE = T([
	'en' => 'PHP console',
	'ru' => 'PHP консоль',
]);
?>
<form method="post">
	<div class="form-group">
		<textarea
			class="form-control"
			name="CODE"
			rows="10"
		><?= htmlspecialchars($_REQUEST['CODE']) ?></textarea>
	</div>
	<input
		type="submit"
		value="<?=T([
		    'en' => 'Execute',
		    'ru' => 'Выполнить',
		])?>"
		class="btn btn-success"
	/>
</form>
<?php
// todo: move to iframe
if (!empty($_REQUEST['CODE'])) {
	echo '<hr/>';
	eval($_REQUEST['CODE']);
}