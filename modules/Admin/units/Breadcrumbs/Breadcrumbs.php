<?php

namespace Admin;

class Breadcrumbs extends \Admin\Menu {

	public function GetActions(array $request = []) {
		return 'Default';
	}

	public function Default() {
		$RESULT['MENU'] = parent::Default();
		$RESULT['BREADCRUMBS'] = $this->ENGINE->BREADCRUMBS;
		return $RESULT;
	}

}
