<?php /** @var \System\Engine $this */ ?>
<html>
<head>
	<title><?= $this->TITLE ?></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<header>
	<div class="page-header" style="text-align: center;">
		<h1>Tigris engine demo</h1>
	</div>
</header>
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-md-2">
			<ul class="nav nav-pills nav-stacked">
				<li class="active"><a href="/">Root</a></li>
				<li><a href="/403/">403</a></li>
				<li><a href="/404/">404</a></li>
				<li><a href="/500/">500</a></li>
				<li><a href="/authorize/">Authorize</a></li>
				<li><a href="/registration/">Register</a></li>
				<li><a href="/profile/">Profile</a></li>
				<li><a href="/forum/">Forum</a></li>
				<li><a href="/u-mail/">U-Mail</a></li>
				<li><a href="/no_wrapper/">No wrapper</a></li>
				<li><a href="/admin/">Admin</a></li>
			</ul>
		</div>
		<div class="col-xs-12 col-md-10">
			<? if (!empty($this->TITLE)): ?>
				<h1><?= $this->TITLE ?></h1>
			<? endif; ?>
			<div class="content">
				<?= $this->CONTENT; ?>
			</div>
		</div>
	</div>
</div>
</body>
</html>
