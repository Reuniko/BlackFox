<?php

namespace BlackFox;

class AdminerUsers extends Adminer {

	public function Execute($PARAMS = [], $REQUEST = []) {
		$PARAMS['SCRUD'] = 'BlackFox\Users';
		parent::Execute($PARAMS, $REQUEST);
	}

	public function Init($PARAMS = []) {
		parent::Init($PARAMS);

		unset($this->SCRUD->fields['SALT']);
		unset($this->SCRUD->fields['HASH']);
		unset($this->SCRUD->fields['PASSWORD']);
		$this->SCRUD->ProvideIntegrity();

		$this->actions += [
			'Login'       => [
				'NAME'        => T([
					'en' => 'Authorize',
					'ru' => 'Авторизоваться',
				]),
				'ICON'        => 'vpn_key',
				'DESCRIPTION' => T([
					'en' => 'Authorize by this user',
					'ru' => 'Авторизоваться под этим пользователем',
				]),
			],
			'SetPassword' => [
				'NAME'   => T([
					'en' => 'Set new password',
					'ru' => 'Установить новый пароль',
				]),
				'ICON'   => 'password',
				'PARAMS' => [
					'password' => [
						'TYPE'     => 'PASSWORD',
						'NAME'     => T([
							'en' => 'Password',
							'ru' => 'Пароль',
						]),
						'NOT_NULL' => true,
					],
				],
			],
		];
	}

	public function Login($ID) {
		User::I()->Login($ID);
		$this->Redirect('/');
	}

	public function SetPassword($ID, $password) {
		Users::I()->SetPassword($ID, $password);
		return T([
			'en' => 'New password has been set',
			'ru' => 'Новый пароль установлен',
		]);
	}
}