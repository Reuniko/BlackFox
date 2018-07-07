<?
/** @var array $value */
/** @var \Admin\Adminer $this */
/** @var string $code */
/** @var \System\SCRUD $Link */
$Link = $this->SCRUD->structure[$code]['LINK']::I();
$url = $Link->GetAdminUrl();
?>
<ul>
	<? foreach ($value as $row): ?>
		<li>
			<nobr>[<a target="_top" href="<?= $url ?>?ID=<?= $row['ID'] ?>"><?= $row['ID'] ?></a>]</nobr>
			<?= $Link->GetElementTitle($row); ?>
		</li>
	<? endforeach; ?>
</ul>
