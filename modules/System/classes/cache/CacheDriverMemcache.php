<?php

namespace System;

/**
 * Class CacheDriverMemcache
 * WIP TODO setup memcache ext
 * @package System
 */
class CacheDriverMemcache extends Cache {

	private $Memcache;

	public function __construct($params = null) {
		if (empty($params)) {
			global $CONFIG;
			$params = $CONFIG['cache']['default'];
		}
		$this->Memcache = new \Memcache();
		foreach ($params['servers'] as $server) {
			$this->Memcache->addServer($server['host'], $server['port']);
		}
	}

	public function Get($key) {
		if (empty($key)) {
			throw new ExceptionCache("Empty key passed");
		}
		$keys = is_array($key) ? $key : [$key];
		//$answer = $this->Memcache->getMulti($keys);
		//debug($this->Memcache->getResultCode(), 'res code');
		//return is_array($key) ? $answer : reset($answer);
	}

	public function Put(string $key, $value, int $ttl = null, array $tags = []) {

	}

	public function Delete(string $key) {

	}

	public function Strike($tags) {

	}

	public function Clear() {

	}
}