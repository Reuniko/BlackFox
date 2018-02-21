<?php

namespace Testing;

class Test extends \System\Instanceable {

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

	public function __construct() {
		$ReflectionClass = new \ReflectionClass(static::class);
		$methods = $ReflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method) {
			if (substr($method->name, 0, 4) === 'Test') {
				$this->tests[$method->name] = trim(substr($method->getDocComment(), 3, -2)) ?: $method->name;
			}
		}
	}

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
			} catch (Exception $error) {
				$this->results[$test] = [
					'NAME'   => $display,
					'STATUS' => 'FAILURE',
					'ERROR'  => $error->getArray(),
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