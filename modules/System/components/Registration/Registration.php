<?php

namespace System;

class Registration extends \System\Component {

	public $options = [
		'CAPTCHA'  => [
			'TYPE'    => 'BOOLEAN',
			'NAME'    => 'Использовать каптчу',
			'DEFAULT' => 'Y',
		],
		'TITLE'    => [
			'TYPE'    => 'STRING',
			'NAME'    => 'Заголовок',
			'DEFAULT' => 'Регистрация',
		],
		'REDIRECT' => [
			'TYPE'        => 'STRING',
			'NAME'        => 'Адрес переадресации',
			'DESCRIPTION' => 'Куда переадресовать пользователя при успешной регистрации',
			'DEFAULT'     => '/',
		],
		'FIELDS'   => [
			'TYPE'    => 'SET',
			'NAME'    => 'Запрашиваемые поля',
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
	];

	public function Init($PARAMS = []) {
		foreach (\System\User::Instance()->structure as $code => $field) {
			if ($code === 'ID') {
				continue;
			}
			$this->options['FIELDS']['VALUES'][$code] = $field['NAME'];
			$this->options['FIELDS']['DEFAULT'][] = $field['NAME'];
		}
		parent::Init($PARAMS);

	}

	public function Work($VALUES = []) {
		$this->Debug($this->PARAMS, '$this->PARAMS');
		$RESULT['FIELDS'] = User::Instance()->ExtractStructure($this->PARAMS['FIELDS']);
		$RESULT['VALUES'] = $VALUES;
		$this->Debug($RESULT, '$RESULT');
		return $RESULT;
	}

	public function Registration($VALUES = []) {
		$this->Debug($VALUES, '$VALUES');
		User::Instance()->Create($VALUES);
		$this->Redirect($this->PARAMS['REDIRECT'], 'Успешная регистрация');
	}
}