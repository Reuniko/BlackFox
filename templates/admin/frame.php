<?php /** @var \System\Engine $this */ ?>
<!DOCTYPE html>
<html>
<head>
	<? require('_header.php'); ?>
	<?= $this->GetHeader(); ?>
	<link href="<?= $this->TEMPLATE_PATH ?>/style.css?<?= filemtime($_SERVER['DOCUMENT_ROOT'] . $this->TEMPLATE_PATH . '/style.css') ?>" rel="stylesheet">
</head>
<body class="frame">
<?= $this->CONTENT; ?>
</body>
</html>
