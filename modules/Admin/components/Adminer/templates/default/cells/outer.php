<?
/** @var string|array $value */
/** @var \Admin\Adminer $this */
/** @var string $code */
/** @var \System\SCRUD $Link */
$Link = $this->SCRUD->structure[$code]['LINK']::I();
$url = $Link->GetAdminUrl();
$ID = $RESULT['DATA'][$code]['ID'];
?>
<? if (!is_array($value)): ?>
	<nobr>[<a href="<?= $url ?>?ID=<?= $value['ID'] ?>"><?= $value['ID'] ?></a>]</nobr>
<? elseif (!empty($value['ID'])): ?>
	<nobr>[<a href="<?= $url ?>?ID=<?= $value['ID'] ?>"><?= $value['ID'] ?></a>]</nobr>
	<? unset($value['ID']) ?>
	<? foreach ($value as $code => $element): ?>
		<?= $element ?>
	<? endforeach; ?>
<? endif; ?>
