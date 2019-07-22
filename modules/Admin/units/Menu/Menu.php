<?php

namespace Admin;

class Menu extends \System\Unit {

	public $options = [
		'BREADCRUMBS' => [
			'TYPE'    => 'boolean',
			'DEFAULT' => true,
		],
	];

	public function GetActions(array $request = []) {
		return 'Default';
	}

	protected function GetMenu() {
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
		return $MENU;
	}

	public function Default() {
		$MENU = $this->GetMenu();

		foreach ($MENU as &$item) {
			$this->SearchActiveItemsRecursive($item, $this->ENGINE->url['path']);
		}

		return $MENU;
	}

	public function SearchActiveItemsRecursive(&$item, $path) {
		$item['ACTIVE'] = $item['CURRENT'] = ($item['LINK'] === $path);

		if (is_array($item['CHILDREN']))
			foreach ($item['CHILDREN'] as &$child)
				$item['ACTIVE'] |= $this->SearchActiveItemsRecursive($child, $path);

		if ($this->PARAMS['BREADCRUMBS'])
			if ($item['ACTIVE'])
				array_unshift($this->ENGINE->BREADCRUMBS, $item);

		return $item['ACTIVE'];
	}

}
