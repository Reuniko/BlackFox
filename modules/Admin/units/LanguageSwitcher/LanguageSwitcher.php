<?php

namespace Admin;

class LanguageSwitcher extends \System\Unit {

	public $options = [
		'LANGUAGES' => [
			'TYPE'    => 'ARRAY',
			'NAME'    => 'Languages',
			'DEFAULT' => [
				'en' => 'EN',
				'ru' => 'RU',
			],
		],
	];

	public function GetActions(array $request = []) {
		if ($request['SwitchLanguage']) {
			return 'SwitchLanguage';
		}
		return 'Default';
	}

	public function Default() {
		$R['LANGUAGES'] = $this->PARAMS['LANGUAGES'];
		$R['LANGUAGE'] = $this->USER->FIELDS['LANG'];
		return $R;
	}

	public function SwitchLanguage($SwitchLanguage) {
		\System\Users::I()->Update($this->USER->ID, ['LANG' => $SwitchLanguage]);
		$_SESSION['USER']['LANG'] = $SwitchLanguage;
		$this->Redirect('?');
	}

}