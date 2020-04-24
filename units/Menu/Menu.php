<?php

namespace BlackFox;

class Menu extends Unit {

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
			$MENU = \BlackFox\Cache::I()->Get('admin_menu');
		} catch (\BlackFox\ExceptionCache $error) {
			$MENU = [];
			foreach ($this->ENGINE->cores as $namespace => $core_relative_path) {
				$Core = "\\{$namespace}\\Core";
				/**@var ACore $Core */
				$MENU = array_merge($MENU, $Core::I()->Menu());
			}
			\BlackFox\Cache::I()->Set('admin_menu', $MENU);
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
