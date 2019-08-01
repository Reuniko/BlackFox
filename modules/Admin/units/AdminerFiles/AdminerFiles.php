<?php

namespace Admin;

class AdminerFiles extends Adminer {

	public function Execute($PARAMS = [], $REQUEST = []) {
		$PARAMS['SCRUD'] = 'System\Files';
		parent::Execute($PARAMS, $REQUEST);
	}

}