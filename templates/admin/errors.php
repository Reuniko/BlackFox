<?php /** @var \System\Engine $this */ ?>
<?php /** @var array $errors */ ?>
<? foreach ($errors as $error): ?>
	<div class="alert alert-danger"><?= $error ?></div>
<? endforeach; ?>