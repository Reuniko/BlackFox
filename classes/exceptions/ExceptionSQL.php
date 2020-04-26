<?php

namespace BlackFox;

class ExceptionSQL extends Exception {

	public $error;
	public $SQL;

	public function __construct($error, $SQL) {

		$this->error = $error;
		$this->SQL = $SQL;

		$message = implode($this->getImplodeSymbols(), [
			$error,
			'<pre>',
			$SQL,
			'</pre>',
		]);

		parent::__construct($message);
	}

}