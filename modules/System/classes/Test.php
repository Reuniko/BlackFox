<?php
namespace System;

class Test extends Instanceable {

	/** @var string имя набора тестов */
	public $name = 'Unknown tests';

	/** @var array $tests ключ — имя метода, значение — расшифровка теста */
	public $tests = [];

	/**
	 * @var array $tests ключ — имя метода, значение:
	 * - NAME - расшифровка теста
	 * - STATUS - SUCCESS|FAILURE
	 * - RESULT - ответ теста
	 * - ERROR - ошибка теста
	 */
	public $results = [];

	public function Run($test) {
		return (new \ReflectionMethod(static::class, $test))->invoke($this);
	}

	public function RunAll() {
		$this->results = [];
		foreach ($this->tests as $test => $display) {
			try {
				$result = $this->Run($test);
				$this->results[$test] = [
					'NAME'   => $display,
					'STATUS' => 'SUCCESS',
					'RESULT' => $result,
				];
			} catch (\Exception $error) {
				$this->results[$test] = [
					'NAME'   => $display,
					'STATUS' => 'FAILURE',
					'ERROR'  => $error->getMessage(),
				];
			}
		}
		return $this->results;
	}
}