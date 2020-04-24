<?php
namespace BlackFox;

class Exception extends \Exception {
	public $array = [];

	/**
	 * Exception constructor.
	 * @param string|array $exception either a string or an array of strings
	 */
	public function __construct($exception = []) {
		if (empty($exception)) {
			$exception = get_called_class();
		}
		if (is_array($exception)) {
			$this->array = $exception;
			$this->message = implode($this->getImplodeSymbols(), $exception);
		}
		if (is_string($exception)) {
			$this->message = $exception;
			$this->array = [$exception];
		}
	}

	public function getArray() {
		return $this->array;
	}

	public function getImplodeSymbols() {
		return (php_sapi_name() === 'cli') ? "\r\n" : '<br/>';
	}
}