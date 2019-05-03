<?php

namespace Testing;

class Test extends \System\Instanceable {

	/** @var string name of the set of tests */
	public $name = 'Unknown tests';

	/** @var array $tests key — method name, value — test description */
	public $tests = [];

	/**
	 * @var array $results key — method name, value:
	 * - NAME - test description
	 * - STATUS - SUCCESS|FAILURE
	 * - RESULT - test answer
	 * - ERROR - test error
	 */
	public $results = [];

	public function __construct() {
		$ReflectionClass = new \ReflectionClass(static::class);
		$methods = $ReflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method) {
			if (substr($method->name, 0, 4) <> 'Test') {
				continue;
			}
			$comment = $method->getDocComment();
			if (!$comment) {
				$this->tests[$method->name] = $method->name;
			} else {
				$this->tests[$method->name] = trim(substr($comment, 3, -2));
			}
		}
	}

	public function Run($test) {
		return (new \ReflectionMethod(static::class, $test))->invoke($this);
	}

	public function RunAll() {
		$this->results = [];
		foreach ($this->tests as $test => $display) {
			$time1 = microtime(true);
			try {
				$result = $this->Run($test);
				$this->results[$test] = [
					'NAME'   => $display,
					'STATUS' => 'SUCCESS',
					'RESULT' => $result,
				];
			} catch (\System\ExceptionSQL $error) {
				$this->results[$test] = [
					'NAME'   => $display,
					'STATUS' => 'FAILURE',
					'RESULT' => $error->getMessage() . ":\r\n<pre>" . $error->SQL . "</pre>",
				];
			} catch (Exception $error) {
				$this->results[$test] = [
					'NAME'   => $display,
					'STATUS' => 'FAILURE',
					'RESULT' => $error->getArray(),
				];
			} catch (\Exception $error) {
				$this->results[$test] = [
					'NAME'   => $display,
					'STATUS' => 'FAILURE',
					'RESULT' => $error->getMessage(),
				];
			}
			$time2 = microtime(true);
			$this->results[$test]['TIME'] = ceil(($time2 - $time1) * 10) / 10;

			if (is_array($this->results[$test]['RESULT']) and count($this->results[$test]['RESULT']) === 1) {
				$this->results[$test]['RESULT'] = reset($this->results[$test]['RESULT']);
			}
		}
		return $this->results;
	}
}