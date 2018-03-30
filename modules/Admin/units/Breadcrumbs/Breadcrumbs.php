<?php

namespace Admin;

class Breadcrumbs extends \Admin\Menu {

	public function GetActions(array $request = []) {
		return 'Work';
	}

	public function Work() {
		$RESULT['MENU'] = parent::Work();
		$RESULT['BREADCRUMBS'] = $this->ENGINE->BREADCRUMBS;
		return $RESULT;
	}

}
