<?php
/**@var BlackFox\Engine $this */
$this->TITLE = T([
	'en' => 'Control panel',
	'ru' => 'Панель управления',
]);


foreach ($this->cores as $namespace => $core_absolute_folder) {
	$Core = "{$namespace}\\Core";
	/* @var \BlackFox\ACore $Core */
	$Scheme = $Core::I()->GetScheme();
	if (is_object($Scheme)) {

		$diff = $Scheme->Synchronize();
		echo "<h4>{$namespace}</h4>";
		echo '<pre>';
		print_r($diff);
		echo '</pre>';
	}
}
