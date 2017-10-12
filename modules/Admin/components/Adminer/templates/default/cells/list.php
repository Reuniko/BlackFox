<? /** @var array $value */ ?>
<? /** @var array $row */ ?>
<? /** @var string $code */ ?>
<?
if (empty($value)) {
	return;
}
?>
<ul class="list">
	<? foreach ($value as $element): ?>
		<li><?= $element ?></li>
	<? endforeach; ?>
</ul>
