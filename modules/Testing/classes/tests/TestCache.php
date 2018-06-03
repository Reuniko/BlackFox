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

	public function TestSetAndGetBoolean() {
		$value = rand(0, 1) ? true : false;
		$this->CACHE->Set('test_boolean', $value);
		$result = $this->CACHE->Get('test_boolean');
		if ($result === $value) {
			return 'OK';
		}
		throw new Exception([var_export($value, true), var_export($result, true)]);
	}

	public function TestSetAndGetInteger() {
		$value = rand(0, 9999);
		$this->CACHE->Set('test_integer', $value);
		$result = $this->CACHE->Get('test_integer');
		if ($result === $value) {
			return $result;
		}
		throw new Exception([var_export($value, true), var_export($result, true)]);
	}

	public function TestSetAndGetString() {
		$value = sha1(random_bytes(8));
		$this->CACHE->Set('test_string', $value);
		$result = $this->CACHE->Get('test_string');
		if ($result === $value) {
			return $result;
		}
		throw new Exception([var_export($value, true), var_export($result, true)]);
	}

	public function TestSetAndGetArray() {
		$value = [1, 2, 's', 'XXX' => [1, 2, '3', '*', ' * instanced']];
		$this->CACHE->Set('test_array', $value);
		$result = $this->CACHE->Get('test_array');
		if ($result === $value) {
			return 'OK';
		}
		throw new Exception($result);
	}

	public function TestSetAndGetObject() {
		$random_user_id = \System\Users::I()->Read([], ['ID'], ['RAND()' => 'ASC'])['ID'];
		$value = new \System\User($random_user_id);
		$this->CACHE->Set('test_object', $value);
		$result = $this->CACHE->Get('test_object');
		if ($result == $value) {
			return $result->ID;
		}
		throw new Exception($result);
	}

	public function TestTagsStrikeOddAndEvenNumbers() {
		foreach (array_fill(1, 20, 0) as $index => $value) {
			$tag = ($index % 2) ? 'test_number_odd' : 'test_number_even';
			$this->CACHE->Replace("test_number_{$index}", $index, null, [$tag, 'test_numbers']);
		}
		$this->CACHE->Strike('test_number_even');
		try {
			$this->CACHE->Get('test_number_6');
			throw new Exception("Key 'test_number_6' exist after strike on tag 'test_number_even'");
		} catch (\System\ExceptionCache $error) {
		}
		$value = $this->CACHE->Get('test_number_5');
		return 'OK';
	}

}