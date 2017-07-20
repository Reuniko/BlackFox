<? /** @var string $value */ ?>
<? /** @var array $row */ ?>
<? /** @var string $code */ ?>
<? foreach ($row[$code . '|VALUES'] as $element): ?>
	<span class="set_value"><?= $element ?></span>
<? endforeach; ?>
