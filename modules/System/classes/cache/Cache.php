<?php

namespace System;

/**
 * Class Cache
 *
 * Base cache driver needs to be replaced with any working child
 *
 * @package System
 */
class Cache extends Instanceable {

	public function Get(string $key) {
		throw new ExceptionCache("Value for key '{$key}' not found");
	}

	public function Set(string $key, $value, int $ttl = null, array $tags = []) {
	}

	public function Clean(string $key) {
	}

	public function Replace(string $key, $value, int $ttl = null, array $tags = []) {
		$this->Clean($key);
		$this->Set($key, $value, $ttl, $tags);
	}

	public function Strike($tags) {
	}

	public function Wipe() {
	}
}