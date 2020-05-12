<?php

namespace BlackFox;

class Registration extends Unit {


	public function Init($PARAMS = []) {

		$this->options = [
			'CAPTCHA'       => [
				'TYPE'    => 'BOOLEAN',
				'NAME'    => T([
					'en' => 'Use captcha',
					'ru' => 'Использовать каптчу',
				]),
				'DEFAULT' => true,
			],
			'TITLE'         => [
				'TYPE'    => 'STRING',
				'NAME'    => T([
					'en' => 'Title',
					'ru' => 'Заголовок',
				]),
				'DEFAULT' => T([
					'en' => 'Registration',
					'ru' => 'Регистрация',
				]),
			],
			'AUTHORIZATION' => [
				'TYPE'    => 'STRING',
				'NAME'    => T([
					'en' => 'Authorization url',
					'ru' => 'Адрес входа в систему',
				]),
				'DEFAULT' => '/',
			],
			'REDIRECT'      => [
				'TYPE'        => 'STRING',
				'NAME'        => T([
					'en' => 'Redirect url',
					'ru' => 'Адрес переадресации',
				]),
				'DESCRIPTION' => T([
					'en' => 'Where to redirect the user upon successful registration',
					'ru' => 'Куда переадресовать пользователя при успешной регистрации',
				]),
				'DEFAULT'     => '/',
			],
			'FIELDS'        => [
				'TYPE'    => 'ARRAY',
				'NAME'    => T([
					'en' => 'Requesting fields',
					'ru' => 'Запрашиваемые поля',
				]),
				'VALUES'  => [],
				'DEFAULT' => [
					'LOGIN',
					'PASSWORD',
					'EMAIL',
					'FIRST_NAME',
					'LAST_NAME',
					'MIDDLE_NAME',
				],
			],
			'MANDATORY'     => [
				'TYPE'    => 'ARRAY',
				'NAME'    => T([
					'en' => 'Mandatory fields',
					'ru' => 'Обязательные поля',
				]),
				'VALUES'  => [],
				'DEFAULT' => [
					'LOGIN',
					'PASSWORD',
				],
			],
		];

		foreach (Users::I()->fields as $code => $field) {
			if ($code === 'ID') {
				continue;
			}
			$this->options['FIELDS']['VALUES'][$code] = $field['NAME'];
			$this->options['FIELDS']['DEFAULT'][] = $field['NAME'];
		}
		parent::Init($PARAMS);

	}

	public function Default($VALUES = []) {
		$RESULT['FIELDS'] = Users::I()->ExtractFields($this->PARAMS['FIELDS']);
		$RESULT['VALUES'] = $VALUES;
		$this->ENGINE->TITLE = $this->PARAMS['TITLE'];
		return $RESULT;
	}

	public function Registration($VALUES = []) {
		if ($this->PARAMS['CAPTCHA']) {
			if (!Captcha::I()->Check()) {
				throw new ExceptionCaptcha(T([
					'en' => 'You must pass the captcha',
					'ru' => 'Необходимо пройти капчу',
				]));
			}
		}
		$ID = Users::I()->Create($VALUES);
		User::I()->Login($ID);

		$url = $this->PARAMS['REDIRECT'];
		if ($_SESSION['USER']['REDIRECT']) {
			$url = $_SESSION['USER']['REDIRECT'];
			unset($_SESSION['USER']['REDIRECT']);
		}
		$this->Redirect($url);
	}
}