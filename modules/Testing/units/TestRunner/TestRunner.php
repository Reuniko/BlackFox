<?php

namespace Testing;

class TestRunner extends \System\Unit {
	public $options = [
		'TESTS' => [
			'TYPE' => 'ARRAY',
			'NAME' => 'Test classes in run order',
		],
	];

	public $tests = [];

	public function Init($PARAMS = []) {
		parent::Init($PARAMS);
		foreach ($this->PARAMS['TESTS'] as $test_class_name) {
			if (!is_subclass_of($test_class_name, 'Testing\Test')) {
				throw new Exception("'$test_class_name' must be the child of Testing\\Test");
			}
			$this->tests[$test_class_name] = $test_class_name::I();
		}
	}

	public function Default() {
		$R = [];
		foreach ($this->tests as $test_class_name => $Test) {
			/** @var Test $Test */
			$R[$test_class_name]['NAME'] = $Test->name;
		}
		return $R;
	}

	public function RunAll() {
		$R = [];
		foreach ($this->tests as $test_class_name => $Test) {
			/** @var Test $Test */
			$R[$test_class_name]['NAME'] = $Test->name;
			$R[$test_class_name]['RESULTS'] = $Test->RunAll();
		}
		return $R;
	}

	public function RunOne($test_class_name) {
		$R = [];
		/** @var Test $Test */
		foreach ($this->tests as $name => $Test) {
			$R[$name]['NAME'] = $Test->name;
		}
		$Test = $this->tests[$test_class_name];
		$R[$test_class_name]['RESULTS'] = $Test->RunAll();
		return $R;
	}
}
