<?php

namespace BlackFox;

class AdminerFiles extends Adminer {

	public function Execute($PARAMS = [], $REQUEST = []) {
		$PARAMS['SCRUD'] = 'BlackFox\Files';
		parent::Execute($PARAMS, $REQUEST);
	}

}