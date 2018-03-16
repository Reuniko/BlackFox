<?
/** @var string|array $value */
/** @var \Admin\Adminer $this */
/** @var string $code */
if ($this->SCRUD->structure[$code]['WYSIWYG']) {
	$value = htmlspecialchars_decode($value);
	$value = strip_tags($value);
}
if (mb_strlen($value) > 250) {
	$value = mb_substr($value, 0, 250) . '...';
}
echo $value;