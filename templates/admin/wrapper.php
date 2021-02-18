<?php /** @var \BlackFox\Engine $this */ ?>
<!DOCTYPE html>
<html>
<head>
	<? require('_header.php') ?>
	<?= $this->GetHeader() ?>
	<title><?= $this->TITLE ?></title>
</head>
<body>

<nav class="header p-2">
	<button
		class="btn btn-info d-inline-block d-md-none"
		data-toggle-sidebar=""
	>
		<span class="material-icons">menu</span>
		<span class="d-none d-md-inline-block"><?= T([
				'en' => 'Menu',
				'ru' => 'Меню',
			]) ?></span>
	</button>

	<a class="btn btn-secondary" href="/">
		<span class="material-icons">desktop_windows</span>
		<span class="d-none d-md-inline-block"><?= T([
				'en' => 'Site',
				'ru' => 'Сайт',
			]) ?></span>
	</a>

	<div class="float-right">

		<? if ($this->User->IsAuthorized()): ?>
			<span class="btn-group">
				<a class="btn btn-secondary" href="/admin/BlackFox/Users.php?ID=<?= $this->User->ID ?>">
					<span class="material-icons">person</span>
					<span class="d-none d-md-inline-block"><?= $this->User->FIELDS['LOGIN'] ?></span>
				</a>
				<a class="btn btn-secondary" href="/admin/logout.php" title="<?= T([
					'en' => 'Logout',
					'ru' => 'Выход',
				]) ?>">
					<span class="material-icons">logout</span>
				</a>
			</span>
		<? else: ?>

		<? endif; ?>

		<? \BlackFox\LanguageSwitcher::Run([]); ?>
	</div>
</nav>

<main role="main" class="container-fluid p-0">
	<div class="row no-gutters">
		<div class="sidebar col-12 col-md-2" id="sidebar">
			<? \BlackFox\Menu::Run() ?>
		</div>
		<div class="main col-12 col-md-10 p-3 p-sm-3">
			<? \BlackFox\Breadcrumbs::Run() ?>
			<h1 class="page-header"><?= $this->TITLE ?></h1>
			<?= $this->CONTENT ?>
		</div>
	</div>
</main>

</body>
</html>
