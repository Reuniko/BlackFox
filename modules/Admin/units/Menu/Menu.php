<?php

namespace Admin;

class Menu extends \System\Unit {

	public function GetActions(array $request = []) {
		return 'Default';
	}

	public function Default() {
		try {
			$MENU = \System\Cache::I()->Get('admin_menu');
		} catch (\System\ExceptionCache $error) {
			$MENU = [];
			foreach ($this->ENGINE->modules as $namespace) {
				$Module = "\\{$namespace}\\Module";
				$MENU = array_merge($MENU, $Module::I()->Menu());
			}
			\System\Cache::I()->Set('admin_menu', $MENU);
		}

		$path = parse_url($_SERVER['REQUEST_URI'])['path'];
		foreach ($MENU as &$item) {
			$this->SearchItemRecursive($item, $path);
		}

		return $MENU;
	}

	protected function SearchItemRecursive(&$item, $path) {
		if ($item['LINK'] === $path) {
			$item['ACTIVE'] = true;
			$item['CURRENT'] = true;
		}
		if (is_array($item['CHILDREN'])) {
			foreach ($item['CHILDREN'] as &$child) {
				$item['ACTIVE'] |= $this->SearchItemRecursive($child, $path);
			}
		}
		return $item['ACTIVE'];
	}

}
