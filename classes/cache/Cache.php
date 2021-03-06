<?php

namespace BlackFox;

/**
 * Base cache driver should to be replaced with any working child.
 * To replace classes go to config, section 'overrides'.
 */
class Cache {

	use Instance;

	public function __construct(array $params = []) {
	}

	/**
	 * Method tries to get cached value(s) by it's\their's key(s):
	 * - if key+value has been found - returns the value in original type
	 * - if no such key+value exist - throws ExceptionCache
	 *
	 * @param string|array $key key(s)
	 * @return mixed value(s)
	 * @throws ExceptionCache "Value for key '...' not found"
	 */
	public function Get($key) {
		throw new ExceptionCache("Value for key '{$key}' not found");
	}

	/**
	 * Method tries to save value:
	 * - if no such key+value exist - save it
	 * - if such key already exist - throws ExceptionCache
	 *
	 * @param string $key key
	 * @param mixed $value value
	 * @param int|null $ttl time to live (optional)
	 * @param array $tags tags (optional)
	 * @throws ExceptionCache "Key already exist: '{$key}'"
	 */
	public function Put(string $key, $value, int $ttl = null, array $tags = []) {
	}

	/**
	 * Method cleans key+value if such key exist.
	 * Otherwise does nothing.
	 *
	 * @param string $key key
	 */
	public function Delete(string $key) {
	}

	/**
	 * Method saves value independently of existing keys.
	 *
	 * @param string $key key
	 * @param mixed $value value
	 * @param int|null $ttl time to live (optional)
	 * @param array $tags tags (optional)
	 * @throws ExceptionCache
	 */
	public function Set(string $key, $value, int $ttl = null, array $tags = []) {
		$this->Delete($key);
		$this->Put($key, $value, $ttl, $tags);
	}

	/**
	 * Method deletes all key+value pairs, tagged with passed tag(s).
	 *
	 * @param string|array $tags tag(s)
	 */
	public function Strike($tags) {
	}

	/**
	 * Method cleans all cache completely.
	 */
	public function Clear() {
	}
}