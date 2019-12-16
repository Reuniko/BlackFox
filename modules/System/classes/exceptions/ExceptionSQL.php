<?php

namespace System;

class ExceptionSQL extends Exception {

	public $error;
	public $SQL;

	public function __construct($error, $SQL) {

		$this->error = $error;
		$this->SQL = $SQL;

		global $CONFIG;
		if ($CONFIG['debug']) {
			$message = implode($this->getImplodeSymbols(), [
				$error,
				'<pre>',
				$SQL,
				'</pre>',
			]);
		} else {
			$message = T([
				'en' => 'Database error',
				'ru' => 'Ошибка в базе данных',
			]);
		}

		parent::__construct($message);
	}

}