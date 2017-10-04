<? /** @var string $value */ ?>
<?
$value = strip_tags($value);
if (mb_strlen($value) > 200) {
	$value = mb_substr($value, 0, 200) . '...';
}
echo $value;
