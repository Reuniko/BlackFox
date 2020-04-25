<?php
/** @var $this \BlackFox\Engine */
$url = parse_url($_SERVER['REQUEST_URI']);
$path = explode('/', $url['path']);

if ($path[0] <> '' or $path[1] <> 'admin') {
	return $this->Show404();
}


$namespace = $path[2];
if (!$this->cores[$namespace]) {
	return $this->Show404();
}

$target = $path[3];
if ($x = strpos($target, '.php')) {
	$target = substr($target, 0, $x);
}

$Class = "{$namespace}\\{$target}";
if (is_subclass_of($Class, "BlackFox\\SCRUD")) {
	\BlackFox\Adminer::Run(['SCRUD' => $Class]);
	return;
}
if (is_subclass_of($Class, "BlackFox\\Unit")) {
	$Class::Run();
	return;
}



return $this->Show404();
