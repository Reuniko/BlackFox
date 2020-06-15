<?php /** @var \BlackFox\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<?
/** @var \BlackFox\Type $Type */
$Type = $this->SCRUD->Types[$RESULT['TAB']['CODE']];
/** @var \BlackFox\SCRUD $Link */
$Link = $Type->field['LINK']::N();
$url = $Link->GetAdminUrl();
$params = http_build_query([
	'FRAME'  => $Type->field['INNER_KEY'],
	'FILTER' => [
		$Type->field['INNER_KEY'] => $RESULT['DATA']['ID'],
	],
]);
$params = $this->SanitizeUrl($params);
?>
<iframe
	class="external"
	src="<?= $url ?>?<?= $params ?>"
></iframe>