<?php

namespace BlackFox;

class Breadcrumbs extends Unit {

	public function GetActions(array $request = []) {
		return 'Default';
	}

	public function Default() {
		$RESULT['BREADCRUMBS'] = $this->ENGINE->BREADCRUMBS;
		return $RESULT;
	}

}
