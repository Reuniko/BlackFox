<?php

namespace Testing;

class TestCache extends Test {
	public $name = 'Тест Cache';
	public $CACHE;

	public function __construct() {
		parent::__construct();
		$this->CACHE = \System\Cache::I();
		$this->CACHE->Clear();
	}

	public function TestGetNonExistingKey() {
		try {
			$value = $this->CACHE->Get('non_exising_key');
		} catch (\System\ExceptionCache $error) {
			return $error->GetMessage();
		}
		throw new Exception("non_exising_key exist: {$value}");
	}

	public function TestPutAndGetSingle() {
		$this->CACHE->Put('test_single', 'single');
		$value = $this->CACHE->Get('test_single');
		if ($value === 'single') {
			return 'OK';
		}
		throw new Exception($value);
	}

	public function TestPutAndGetMultiple() {
		$this->CACHE->Put('test_multiple_1', 'multiple_1');
		$this->CACHE->Put('test_multiple_2', 'multiple_2');
		$this->CACHE->Put('test_multiple_3', 'multiple_3');
		$value = $this->CACHE->Get([
			'test_multiple_1',
			'test_multiple_2',
			'test_multiple_3',
		]);
		$awaiting = [
			'test_multiple_1' => 'multiple_1',
			'test_multiple_2' => 'multiple_2',
			'test_multiple_3' => 'multiple_3',
		];
		if ($value === $awaiting) {
			return 'OK';
		}
		throw new Exception($value);
	}

	public function TestPutAndGetMissedMultiple() {
		$this->CACHE->Set('test_multiple_1', 'multiple_1');
		$this->CACHE->Set('test_multiple_2', 'multiple_2');
		$this->CACHE->Set('test_multiple_3', 'multiple_3');
		try {
			$value = $this->CACHE->Get([
				'test_multiple_1',
				'test_multiple_2',
				'test_multiple_3',
				'test_multiple_4',
			]);
		} catch (\System\ExceptionCache $error) {
			return $error->GetMessage();
		}
		throw new Exception('No exception trowed for key "test_multiple_4"');
	}

	public function TestPutAndGetBoolean() {
		$value = rand(0, 1) ? true : false;
		$this->CACHE->Put('test_boolean', $value);
		$result = $this->CACHE->Get('test_boolean');
		if ($result === $value) {
			return 'OK';
		}
		throw new Exception([var_export($value, true), var_export($result, true)]);
	}

	public function TestPutAndGetInteger() {
		$value = rand(0, 9999);
		$this->CACHE->Put('test_integer', $value);
		$result = $this->CACHE->Get('test_integer');
		if ($result === $value) {
			return $result;
		}
		throw new Exception([var_export($value, true), var_export($result, true)]);
	}

	public function TestPutAndGetString() {
		$value = sha1(random_bytes(8));
		$this->CACHE->Put('test_string', $value);
		$result = $this->CACHE->Get('test_string');
		if ($result === $value) {
			return $result;
		}
		throw new Exception([var_export($value, true), var_export($result, true)]);
	}

	public function TestPutAndGetArray() {
		$value = [1, 2, 's', 'XXX' => [1, 2, '3', '*', ' * instanced']];
		$this->CACHE->Put('test_array', $value);
		$result = $this->CACHE->Get('test_array');
		if ($result === $value) {
			return 'OK';
		}
		throw new Exception($result);
	}

	public function TestPutAndGetObject() {
		$random_user_id = \System\Users::I()->Read([], ['ID'], ['RAND()' => 'ASC'])['ID'];
		$value = new \System\User($random_user_id);
		$this->CACHE->Put('test_object', $value);
		$result = $this->CACHE->Get('test_object');
		if ($result == $value) {
			return $result->ID;
		}
		throw new Exception($result);
	}

	public function TestDelete() {
		$this->CACHE->Set('test_delete', 'fweifjiwejfiew');
		$this->CACHE->Delete('test_delete');
		try {
			$value = $this->CACHE->Get('test_delete');
		} catch (\System\ExceptionCache $error) {
			return $error->GetMessage();
		}
		throw new Exception("Key test_delete exist: {$value}");
	}

	public function TestTagsStrikeOddAndEvenNumbers() {
		foreach (array_fill(1, 20, 0) as $index => $value) {
			$tag = ($index % 2) ? 'test_number_odd' : 'test_number_even';
			$this->CACHE->Set("test_number_{$index}", $index, null, [$tag, 'test_numbers']);
		}
		$this->CACHE->Strike('test_number_even');
		try {
			$this->CACHE->Get('test_number_6');
			throw new Exception("Key 'test_number_6' exist after strike on tag 'test_number_even'");
		} catch (\System\ExceptionCache $error) {
		}
		$value = $this->CACHE->Get('test_number_5');
		return 'OK: ' . $value;
	}

}