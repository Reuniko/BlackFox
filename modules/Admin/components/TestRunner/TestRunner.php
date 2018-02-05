<?php
namespace Admin;
class TestRunner extends \System\Component {
	public $options = [
		'TESTS' => [
			'TYPE' => 'ARRAY',
			'NAME' => 'Тестовые классы в порядке запуска',
		],
	];

	public $tests = [];

	public function Init($PARAMS = []) {
		parent::Init($PARAMS);
		foreach ($this->PARAMS['TESTS'] as $test_class_name) {
			if (!is_subclass_of($test_class_name, 'System\Test')) {
				throw new Exception("'$test_class_name' must be the child of System\\Test");
			}
			$this->tests[$test_class_name] = $test_class_name::I();
		}
	}

	public function Work() {
		$R = [];
		foreach ($this->tests as $test_class_name => $Test) {
			/** @var \System\Test $Test */
			$R[$test_class_name]['NAME'] = $Test->name;
		}
		return $R;
	}

	public function RunAll() {
		$R = [];
		foreach ($this->tests as $test_class_name => $Test) {
			/** @var \System\Test $Test */
			$R[$test_class_name]['NAME'] = $Test->name;
			$R[$test_class_name]['RESULTS'] = $Test->RunAll();
		}
		return $R;
	}
}
