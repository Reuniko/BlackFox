<?php /** @var \System\Engine $this */ ?>
<!DOCTYPE html>
<html>
<head>
	<? require('_header.php') ?>
	<?= $this->GetHeader() ?>
	<link href="<?= $this->TEMPLATE_PATH ?>/style.css?<?= filemtime($_SERVER['DOCUMENT_ROOT'] . $this->TEMPLATE_PATH . '/style.css') ?>" rel="stylesheet">
</head>
<body>

<nav class="header p-2">
	<button
		class="btn btn-info d-inline-block d-md-none"
		data-toggle-sidebar=""
	>
		<i class="fa fa-bars"></i>
		<span class="d-none d-md-inline-block">Меню</span>
	</button>

	<a class="btn btn-secondary" href="/">
		<i class="fa fa-desktop"></i>
		<span class="d-none d-md-inline-block">Сайт</span>
	</a>

	<div class="float-right">

		<a class="btn btn-secondary" href="/admin/System/Users.php?ID=<?= \System\User::I()->ID ?>">
			<i class="fa fa-user"></i>
			<span class="d-none d-md-inline-block"><?= \System\User::I()->FIELDS['LOGIN'] ?></span>
		</a>

		<a class="btn btn-secondary" href="/admin/logout.php">
			<i class="fa fa-sign-out-alt"></i>
			<span class="d-none d-md-inline-block">Выход</span>
		</a>

	</div>
</nav>

<main role="main" class="container-fluid p-0">
	<div class="row no-gutters">
		<div class="sidebar col-12 col-sm-2" id="sidebar">
			<? \Admin\Menu::Run() ?>
		</div>
		<div class="main col-12 col-sm-10 px-3">
			<? \Admin\Breadcrumbs::Run() ?>
			<h1 class="page-header"><?= $this->TITLE ?></h1>
			<?= $this->CONTENT ?>
		</div>
	</div>
</main>

</body>
</html>
