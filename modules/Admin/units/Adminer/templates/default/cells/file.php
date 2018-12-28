<? /** @var array $value */ ?>
<? debug($value) ?>
<? if (!empty($value)): ?>
	[<a target="_blank" href="/admin/System/Files.php?ID=<?= $value['ID'] ?>"><?= $value['ID'] ?></a>]
	<a target="_blank" href="<?= $value['SRC'] ?>"><?= $value['NAME'] ?></a>
<? endif; ?>

