<?php

namespace BlackFox;

class CaptchaGoogleRecaptchaV2 extends Captcha {

	public $key;
	private $secret;

	public function __construct(Engine $Engine) {
		$this->key = $Engine->config['google_recaptcha']['key'];
		$this->secret = $Engine->config['google_recaptcha']['secret'];
		if (empty($this->key) or empty($this->secret)) {
			throw new ExceptionCaptcha(T([
				'en' => 'Specify config keys: google_recaptcha->key, google_recaptcha->secret',
				'ru' => 'Укажите ключи в конфигурации: google_recaptcha->key, google_recaptcha->secret',
			]));
		}
		$Engine->AddHeaderScript('https://www.google.com/recaptcha/api.js');
	}

	public function Show($params = []) {
		?>
		<div class="g-recaptcha <?= $params['CSS_CLASS'] ?>" data-sitekey="<?= $this->key ?>"></div>
		<?
	}

	public function Check($response = null, $remoteip = null) {
		$response = is_null($response) ? $_REQUEST['g-recaptcha-response'] : $response;
		$context = stream_context_create(['http' => [
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => http_build_query([
				'secret'   => $this->secret,
				'response' => $response,
				'remoteip' => $remoteip ?: $_SERVER['REMOTE_ADDR'],
			]),
		]]);
		$result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
		$result = json_decode($result, true);
		return $result['success'];
	}

}