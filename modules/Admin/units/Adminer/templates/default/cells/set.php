<? /** @var string $value */ ?>
<? /** @var array $row */ ?>
<? /** @var string $code */ ?>
<?
if (empty($value)) {
	return;
}
?>
<ul class="set">
	<? foreach ($row[$code . '|VALUES'] as $element): ?>
		<li><?= $element ?></li>
	<? endforeach; ?>
</ul>
