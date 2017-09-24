<?php

namespace System;

class ExceptionSQL extends Exception {
	public $SQL = null;

	public function __construct($exception, $SQL) {
		parent::__construct($exception);
		$this->SQL = $SQL;
	}
}