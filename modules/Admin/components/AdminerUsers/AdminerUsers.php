<?php

namespace Admin;

class AdminerUsers extends Adminer {

	public function Section($FILTER = [], $PAGE = 1, $SORT = ['ID' => 'DESC'], $FIELDS = ['**'], $popup = null) {
		unset($this->SCRUD->structure['SALT']);
		unset($this->SCRUD->structure['PASSWORD']);
		return parent::Section($FILTER, $PAGE, $SORT, $FIELDS, $popup);
	}

	public function Element($ID = 0, $FIELDS = []) {
		unset($this->SCRUD->composition['SYSTEM']['FIELDS']['SALT']);
		return parent::Element($ID, $FIELDS);
	}
}