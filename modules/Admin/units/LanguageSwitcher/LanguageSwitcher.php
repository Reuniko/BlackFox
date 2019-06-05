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
		$R['LANGUAGE'] = $this->ENGINE->GetLanguage();
		return $R;
	}

	public function SwitchLanguage($SwitchLanguage) {
		$this->ENGINE->SetLanguage($SwitchLanguage);
		$this->Redirect('?' . http_build_query(array_diff($_GET, ['SwitchLanguage' => $SwitchLanguage])));
	}

}