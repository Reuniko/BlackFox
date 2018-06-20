<? /** @var string $value */ ?>
<? if (is_array($value) and !empty($value)): ?>
	<pre><?= print_r($value, true) ?></pre>
<? endif; ?>
