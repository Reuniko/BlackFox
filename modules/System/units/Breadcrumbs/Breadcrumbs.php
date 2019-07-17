<?php

namespace System;

class Breadcrumbs extends \System\Unit {

	public function GetActions(array $request = []) {
		return 'Default';
	}

	public function Default() {
		$RESULT['BREADCRUMBS'] = $this->ENGINE->BREADCRUMBS;
		return $RESULT;
	}

}
