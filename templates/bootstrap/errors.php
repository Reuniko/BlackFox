<?php /** @var \System\Engine $this */ ?>
<?php /** @var array $errors */ ?>
<div class="jumbotron">
	<? foreach ($errors as $error): ?>
		<div class="alert alert-danger"><?= $error ?></div>
	<? endforeach; ?>
</div>