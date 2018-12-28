<?php

namespace System;

class Registration extends \System\Unit {

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
		foreach (Users::Instance()->structure as $code => $field) {
			if ($code === 'ID') {
				continue;
			}
			$this->options['FIELDS']['VALUES'][$code] = $field['NAME'];
			$this->options['FIELDS']['DEFAULT'][] = $field['NAME'];
		}
		parent::Init($PARAMS);

	}

	public function Work($VALUES = []) {
		$RESULT['FIELDS'] = Users::Instance()->ExtractStructure($this->PARAMS['FIELDS']);
		$RESULT['VALUES'] = $VALUES;
		return $RESULT;
	}

	public function Registration($VALUES = []) {
		$ID = Users::Instance()->Create($VALUES);
		User::I()->Login($ID);
		$this->Redirect($this->PARAMS['REDIRECT']);
	}
}