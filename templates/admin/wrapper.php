<?php /** @var \System\Engine $this */ ?>
<!DOCTYPE html>
<html>
<head>
	<? require('_header.php'); ?>
	<?= $this->GetHeader(); ?>
	<link href="<?= $this->TEMPLATE_PATH ?>/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
	<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<a class="navbar-brand" href="/">Site</a>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
		</ul>
		<form class="form-inline my-2 my-lg-0">
			<input class="form-control mr-sm-2" type="text" placeholder="Search">
			<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
		</form>
	</div>
</nav>

<div class="container-fluid p-0">
	<div class="row no-gutters">
		<div class="col-sm-3 col-md-2 sidebar">
			<? \Admin\Menu::Run(); ?>
		</div>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main p-3">
			<h1 class="page-header"><?= $this->TITLE ?></h1>
			<?= $this->CONTENT; ?>
		</div>
	</div>
</div>

</body>
</html>
