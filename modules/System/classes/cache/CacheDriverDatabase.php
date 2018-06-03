<?php

namespace System;

class CacheDriverDatabase extends Cache {

	public $DATA;
	public $TAGS;

	public function __construct() {
		$this->DATA = CacheData::I();
		$this->TAGS = CacheTags::I();
		$this->DATA->Delete(['<EXPIRE' => time()]);
	}

	private function PackValue($value) {
		$type = gettype($value);
		if (in_array($type, ['boolean', 'integer', 'double', 'string'])) {
			return $value;
		}
		if ($type === 'array') {
			return json_encode($value);
		}
		if ($type === 'object') {
			return serialize($value);
		}
		throw new ExceptionCache("Unknown type for cache: '{$type}'");
	}

	private function UnpackValue($value, $type) {
		if ($type === 'boolean') {
			return (bool)$value;
		}
		if ($type === 'integer') {
			return (integer)$value;
		}
		if ($type === 'double') {
			return (double)$value;
		}
		if ($type === 'string') {
			return (string)$value;
		}
		if ($type === 'array') {
			return json_decode($value, true);
		}
		if ($type === 'object') {
			return unserialize($value);
		}
		throw new ExceptionCache("Unknown type for cache: '{$type}'");
	}

	public function Get($key) {
		if (empty($key)) {
			throw new ExceptionCache("Empty key passed");
		}
		$keys = is_array($key) ? $key : [$key];
		$data = $this->DATA->GetList([
			'FILTER' => ['KEY' => $keys],
			'FIELDS' => ['KEY', 'VALUE', 'TYPE'],
			'ESCAPE' => false,
		]);
		$answer = [];
		foreach ($keys as $key_i) {
			if (empty($data[$key_i])) {
				throw new ExceptionCache("Value for key '{$key_i}' not found");
			}
			$answer[$key_i] = $this->UnpackValue($data[$key_i]['VALUE'], $data[$key_i]['TYPE']);
		}
		if (is_array($key)) {
			return $answer;
		} else {
			return reset($answer);
		}
	}

	public function Put(string $key, $value, int $ttl = null, array $tags = []) {
		try {
			$this->DATA->Create([
				'KEY'    => $key,
				'TYPE'   => gettype($value),
				'VALUE'  => $this->PackValue($value),
				'EXPIRE' => $ttl ? time() + $ttl : null,
			]);
		} catch (Exception $error) {
			throw new ExceptionCache("Key exist: '{$key}'");
		}
		foreach ($tags as $tag) {
			$this->TAGS->Create([
				'KEY' => $key,
				'TAG' => $tag,
			]);
		}
	}

	public function Delete(string $key) {
		$this->DATA->Delete(['KEY' => $key]);
		$this->TAGS->Delete(['KEY' => $key]);
	}

	public function Strike($tags) {
		$tags = is_array($tags) ? $tags : [$tags];
		$keys = $this->TAGS->Select(['TAG' => $tags], [], 'KEY');
		$this->DATA->Delete(['KEY' => $keys]);
		$this->TAGS->Delete(['TAG' => $tags]);
		$this->TAGS->Delete(['KEY' => $keys]);
	}

	public function Clear() {
		$this->DATA->Truncate();
		$this->TAGS->Truncate();
	}
}
