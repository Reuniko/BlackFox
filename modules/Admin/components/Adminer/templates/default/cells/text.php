<? /** @var string $value */ ?>
<?
$value = strip_tags($value);
if (mb_strlen($value) > 100) {
	$value = mb_substr($value, 0, 100) . '...';
}
echo $value;
?>
