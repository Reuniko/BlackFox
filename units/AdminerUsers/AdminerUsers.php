<?php

namespace BlackFox;

class AdminerUsers extends Adminer {

	public function Execute($PARAMS = [], $REQUEST = []) {
		$PARAMS['SCRUD'] = 'BlackFox\Users';
		parent::Execute($PARAMS, $REQUEST);
	}

	public function Section($FILTER = [], $PAGE = 1, $SORT = ['ID' => 'DESC'], $FIELDS = ['*@@']) {
		unset($this->SCRUD->structure['SALT']);
		unset($this->SCRUD->composition['SYSTEM']['FIELDS']['HASH']);
		unset($this->SCRUD->structure['PASSWORD']);
		return parent::Section($FILTER, $PAGE, $SORT, $FIELDS);
	}

	public function CreateForm($FILTER = [], $FIELDS = []) {
		unset($this->SCRUD->composition['SYSTEM']['FIELDS']['SALT']);
		unset($this->SCRUD->composition['SYSTEM']['FIELDS']['HASH']);
		return parent::CreateForm($FILTER, $FIELDS);
	}

	public function UpdateForm($ID = null, $FIELDS = []) {
		unset($this->SCRUD->composition['SYSTEM']['FIELDS']['SALT']);
		unset($this->SCRUD->composition['SYSTEM']['FIELDS']['HASH']);
		return parent::UpdateForm($ID, $FIELDS);
	}

	public function Login($ID) {
		\BlackFox\User::I()->Login($ID);
		$this->Redirect('/');
	}

	public function GetTabsOfUpdate() {
		return parent::GetTabsOfUpdate() + [
				'actions' => [
					'NAME' => T([
						'en' => 'Actions',
						'ru' => 'Действия',
					]),
					'VIEW' => 'element_tab_actions',
				],
			];
	}
}