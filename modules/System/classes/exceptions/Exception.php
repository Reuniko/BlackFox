<?php
namespace System;

class Exception extends \Exception {
	public $array = [];

	/**
	 * Exception constructor.
	 * @param string|array $exception
	 */
	public function __construct($exception = []) {
		if (empty($exception)) {
			$exception = get_called_class();
		}
		if (is_array($exception)) {
			$this->array = $exception;
			$this->message = implode("<br/>", $exception); // TODO <br/> or \r\n depend on CONTEXT
		}
		if (is_string($exception)) {
			$this->message = $exception;
			$this->array = [$exception];
		}
	}

	public function getArray() {
		return $this->array;
	}
}