<?php

namespace BlackFox;

class LanguageSwitcher extends Unit {

	public function GetActions(array $request = []) {
		if ($request['SwitchLanguage']) {
			return 'SwitchLanguage';
		}
		return 'Default';
	}

	public function Default() {
		if (count($this->ENGINE->languages) <= 1) {
			$this->view = null;
			return null;
		}
		$R['LANGUAGES'] = $this->ENGINE->languages;
		$R['LANGUAGE'] = $this->ENGINE->GetLanguage();
		return $R;
	}

	public function SwitchLanguage($SwitchLanguage) {
		$this->ENGINE->SetLanguage($SwitchLanguage);
		$request = array_diff($_GET, ['SwitchLanguage' => $SwitchLanguage]);
		$this->Redirect(empty($request) ? '.' : '?' . http_build_query($request));
	}

}