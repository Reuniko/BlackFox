<?php

namespace BlackFox;
class Captcha extends Instanceable {

	public function Show($params = []) {
		return;
		throw new ExceptionCaptcha(T([
			'en' => 'Override default class "Captcha" with driver',
			'ru' => 'Переопределите класс "Captcha" драйвером',
		]));
	}

	public function Check() {
		return;
		throw new ExceptionCaptcha(T([
			'en' => 'Override default class "Captcha" with driver',
			'ru' => 'Переопределите класс "Captcha" драйвером',
		]));
	}

}