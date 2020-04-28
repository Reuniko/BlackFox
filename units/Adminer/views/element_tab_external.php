<?php /** @var \BlackFox\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<?
/** @var \BlackFox\Type $Type */
$Type = $this->SCRUD->Types[$RESULT['TAB']['CODE']];
/** @var \BlackFox\SCRUD $Link */
$Link = $Type->info['LINK']::N();
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