<?php

namespace System;

class Authorization extends \System\Component {

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

	public function SelectMethodForView($request = []) {
		return 'Form';
	}

	public function SelectMethodForAction($request = []) {
		if ($request['ACTION'] === 'Login') {
			return 'Login';
		}
		return false;
	}

	public function Form($login = null, $password = null) {
		return [
			'LOGIN'    => $login,
			'PASSWORD' => $password,
		];
	}

	public function Login($login = null, $password = null) {
		User::I()->Authorization($login, $password);
		$this->Redirect(false, "Вы успешно авторизированы");
	}

}
