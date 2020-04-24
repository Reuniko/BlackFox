<?php /** @var \BlackFox\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<?
/** @var \BlackFox\Type $Type */
$Type = $this->SCRUD->structure[$RESULT['TAB']['CODE']];
/** @var \BlackFox\SCRUD $Link */
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