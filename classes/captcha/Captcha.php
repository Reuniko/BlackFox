<?php

namespace BlackFox;
class Captcha {

	use Instance;

	public function Show($params = []) {
		return;
		throw new ExceptionCaptcha(T([
			'en' => 'Override default class "Captcha" with driver',
			'ru' => 'Переопределите класс "Captcha" драйвером',
		]));
	}

	public function Check() {
		return false;
	}

}