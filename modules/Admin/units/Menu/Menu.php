<?php

namespace Admin;

class Menu extends \System\Unit {

	protected $path;

	public function GetActions(array $request = []) {
		return 'Work';
	}

	public function Work() {
		$this->RESULT = [];
		foreach ($this->ENGINE->modules as $module => $info) {
			try {
				$menu = require($this->ENGINE->GetCoreFile("modules/{$module}/.menu.php"));
			} catch (\Exception $error) {
				continue;
			}
			$this->RESULT = array_merge($this->RESULT, $menu);
		}
		$this->path = parse_url($_SERVER['REQUEST_URI'])['path'];

		foreach ($this->RESULT as &$item) {
			$this->SearchItemRecursive($item);
		}

		return $this->RESULT;
	}

	protected function SearchItemRecursive(&$item) {
		if ($item['LINK'] === $this->path) {
			$item['ACTIVE'] = true;
			$item['CURRENT'] = true;
		}
		if (is_array($item['CHILDREN'])) {
			foreach ($item['CHILDREN'] as &$child) {
				$item['ACTIVE'] |= $this->SearchItemRecursive($child);
			}
		}
		return $item['ACTIVE'];
	}


}
