<?php

namespace System;

class ExceptionSQL extends Exception {

	public function __construct($exception, $SQL) {
		global $CONFIG;
		if ($CONFIG['debug']) {
			$message = implode($this->getImplodeSymbols(), [
				$exception,
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

		try {
			Log::I()->Create([
				'TYPE'    => static::class,
				'MESSAGE' => $exception,
				'DATA'    => $SQL,
			]);
		} catch (\Exception $error) {
		}
	}

}