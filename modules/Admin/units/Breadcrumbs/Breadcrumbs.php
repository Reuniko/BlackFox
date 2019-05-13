<?php

namespace Admin;

class Breadcrumbs extends Menu {

	public function GetActions(array $request = []) {
		return 'Default';
	}

	public function Default() {
		$RESULT['MENU'] = parent::Default();
		$RESULT['BREADCRUMBS'] = $this->ENGINE->BREADCRUMBS;
		return $RESULT;
	}

}
