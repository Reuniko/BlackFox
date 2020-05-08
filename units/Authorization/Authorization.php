<?php

namespace BlackFox;

class Authorization extends Unit {

	public function __construct() {
		parent::__construct();
		$this->name = T([
			'en' => 'Authorization',
			'ru' => 'Авторизация',
		]);
		$this->description = T([
			'en' => 'Provides a form and authorization mechanism on the site',
			'ru' => 'Предоставляет форму и механизм авторизации на сайте',
		]);
		$this->options = [
			'CAPTCHA'      => [
				'TYPE'    => 'BOOLEAN',
				'NAME'    => T([
					'en' => 'Use captcha',
					'ru' => 'Использовать каптчу',
				]),
				'DEFAULT' => true,
			],
			'MESSAGE'      => [
				'TYPE'    => 'STRING',
				'NAME'    => T([
					'en' => 'Message',
					'ru' => 'Сообщение',
				]),
				'DEFAULT' => '',
			],
			'TITLE'        => [
				'TYPE'    => 'STRING',
				'NAME'    => T([
					'en' => 'Title',
					'ru' => 'Заголовок',
				]),
				'DEFAULT' => T([
					'en' => 'Authorization',
					'ru' => 'Авторизация',
				]),
			],
			'REDIRECT'     => [
				'TYPE'        => 'STRING',
				'NAME'        => T([
					'en' => 'Redirect',
					'ru' => 'Переадресация',
				]),
				'DESCRIPTION' => T([
					'en' => 'Where to redirect the user upon successful authorization',
					'ru' => 'Куда переадресовать пользователя при успешной авторизации',
				]),
				'DEFAULT'     => null,
			],
			'REGISTRATION' => [
				'TYPE'        => 'STRING',
				'NAME'        => T([
					'en' => 'Registration',
					'ru' => 'Регистрация',
				]),
				'DESCRIPTION' => T([
					'en' => 'Link to registration',
					'ru' => 'Ссылка на регистрацию',
				]),
				'DEFAULT'     => '/',
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

	public function Form($login = null, $password = null, $redirect = null) {
		$this->ENGINE->TITLE = $this->name;
		if ($this->PARAMS['MESSAGE'] and empty($this->ALERTS)) {
			$this->ALERTS[] = ['TYPE' => 'info', 'TEXT' => $this->PARAMS['MESSAGE']];
		}
		if (!empty($redirect)) {
			$_SESSION['USER']['REDIRECT'] = $redirect;
		}
		return [
			'LOGIN'    => $login,
			'PASSWORD' => $password,
		];
	}

	public function Login($login = null, $password = null) {
		if ($this->PARAMS['CAPTCHA']) {
			Captcha::I()->Check();
		}
		User::I()->Authorization($login, $password);

		$url = $this->PARAMS['REDIRECT'];
		if ($_SESSION['USER']['REDIRECT']) {
			$url = $_SESSION['USER']['REDIRECT'];
			unset($_SESSION['USER']['REDIRECT']);
		}
		$this->Redirect($url);
	}

}
