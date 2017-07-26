element.php redefinded
<?
$debug = debug_backtrace();
// debug($debug, '$debug');
foreach ($debug as $item) {
	debug($item);
}
?>