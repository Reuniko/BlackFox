<?php /** @var \BlackFox\Engine $this */ ?>
<!DOCTYPE html>
<html>
<head>
	<? require('_header.php'); ?>
	<?= $this->GetHeader(); ?>
	<link href="<?= $this->TEMPLATE_PATH ?>/style.css?<?= filemtime($_SERVER['DOCUMENT_ROOT'] . $this->TEMPLATE_PATH . '/style.css') ?>" rel="stylesheet">
	<title><?= $this->TITLE ?></title>
</head>
<body class="frame p-1 p-sm-2">
<?= $this->CONTENT; ?>
</body>
</html>
