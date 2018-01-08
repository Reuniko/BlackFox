<?php

namespace Admin;

class AdminerFiles extends Adminer {

	public function Execute($PARAMS = []) {
		$PARAMS['SCRUD'] = 'System\Files';
		parent::Execute($PARAMS);
	}

}