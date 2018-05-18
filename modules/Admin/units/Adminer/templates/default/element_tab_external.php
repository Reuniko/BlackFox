<?php /** @var \Admin\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<?
/** @var \System\Type $Type */
$Type = $this->SCRUD->types[$RESULT['TAB']['CODE']];
/** @var \System\SCRUD $Link */
$Link = new $Type->info['LINK'];
$url = $Link->GetAdminUrl();
$params = http_build_query([
	'FRAME'  => $Type->info['FIELD'],
	'FILTER' => [
		$Type->info['FIELD'] => $RESULT['DATA']['ID'],
	],
]);
?>
<iframe
	class="external"
	src="<?= $url ?>?<?= $params ?>"
></iframe>