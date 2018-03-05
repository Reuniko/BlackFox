<?php
/** @var $this \System\Engine */

$url = parse_url($_SERVER['REQUEST_URI']);
$path = explode('/', $url['path']);

if ($path[0] <> '' or $path[1] <> 'admin') {
	return $this->Show404();
}

$module = $path[2];
if (empty($this->modules[$module])) {
	return $this->Show404();
}

$target = $path[3];
if ($x = strpos($target, '.php')) {
	$target = substr($target, 0, $x);
}

$Class = "{$module}\\{$target}";
if (is_subclass_of($Class, "System\\SCRUD")) {
	\Admin\Adminer::Run(['SCRUD' => $Class]);
	return;
}


return $this->Show404();
