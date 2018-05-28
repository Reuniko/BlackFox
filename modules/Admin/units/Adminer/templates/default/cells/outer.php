<?
/** @var string|array $value */
/** @var \Admin\Adminer $this */
/** @var string $code */
/** @var \System\SCRUD $Link */
$Link = $this->SCRUD->structure[$code]['LINK']::I();
$url = $Link->GetAdminUrl();
?>
<? if (!is_array($value)): ?>
	<nobr>[<a target="_top" href="<?= $url ?>?ID=<?= $value ?>"><?= $value ?></a>]</nobr>
<? elseif (!empty($value['ID'])): ?>
	<nobr>[<a target="_top" href="<?= $url ?>?ID=<?= $value['ID'] ?>"><?= $value['ID'] ?></a>]</nobr>
	<?= $Link->GetElementTitle($value); ?>
<? endif; ?>
