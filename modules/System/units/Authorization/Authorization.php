<?php

namespace System;

class Authorization extends \System\Unit {

	public function __construct() {
		parent::__construct();
		$this->name = 'Авторизация';
		$this->description = 'Предоставляет форму и механизм авторизации на сайте';
		$this->options = [
			'CAPTCHA'  => [
				'TYPE'    => 'BOOLEAN',
				'NAME'    => 'Использовать каптчу',
				'DEFAULT' => 'Y',
			],
			'MESSAGE'  => [
				'TYPE'    => 'STRING',
				'NAME'    => 'Сообщение',
				'DEFAULT' => '',
			],
			'TITLE'    => [
				'TYPE'    => 'STRING',
				'NAME'    => 'Заголовок',
				'DEFAULT' => 'Вход в систему',
			],
			'REDIRECT' => [
				'TYPE'        => 'STRING',
				'NAME'        => 'Переадресация',
				'DESCRIPTION' => 'Куда переадресовать пользователя при успешной авторизации',
				'DEFAULT'     => '/profile/welcome.php',
			],
		];
		$this->allow_ajax_request = true;
		$this->allow_json_request = true;
	}

	public function GetActions(array $request = []) {
		if ($request['ACTION'] === 'Login') {
			return ['Login', 'Form'];
		}
		return ['Form'];
	}

	public function Form($login = null, $password = null) {
		if ($this->PARAMS['MESSAGE'] and empty($this->ALERTS)) {
			$this->ALERTS[] = ['TYPE' => 'info', 'TEXT' => $this->PARAMS['MESSAGE']];
		}
		return [
			'LOGIN'    => $login,
			'PASSWORD' => $password,
		];
	}

	public function Login($login = null, $password = null) {
		User::I()->Authorization($login, $password);
		$this->Redirect($this->PARAMS['REDIRECT']);
	}

}
