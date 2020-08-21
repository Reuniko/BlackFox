<?php

namespace BlackFox;

/**
 * Class CacheDriverRedis
 * @package BlackFox
 */
class CacheRedis extends Cache {

	private $example_config = [
		'cache' => [
			'hosts' => [[
				'host'    => '127.0.0.1',
				'port'    => 6379,
				'timeout' => 1,
			]],
		],
	];

	/**@var \Redis $Redis */
	private $Redis;

	public function __construct(array $params = []) {
		parent::__construct($params);
		$this->Redis = new \Redis();
		foreach ($params['hosts'] as $host) {
			@$result = $this->Redis->connect(
				$host['host'],
				$host['port'],
				$host['timeout'],
				$host['retry_interval']
			);
			if (!$result) {
				throw new ExceptionCache("Can't connect to Redis server {$host['host']}:{$host['port']}");
			}
		}
		if (!empty($params['password'])) {
			$this->Redis->auth($params['password']);
		}
		if (!empty($params['dbindex'])) {
			$this->Redis->select($params['dbindex']);
		}
	}

	public function Get($key) {
		if (empty($key)) {
			throw new ExceptionCache("Empty key passed");
		}
		$keys = is_array($key) ? $key : [$key];
		$answer = [];
		foreach ($keys as $key_i) {
			$answer[$key_i] = $this->Redis->get("val|{$key_i}");
			if ($answer[$key_i] === false) {
				throw new ExceptionCache("Value for key '{$key_i}' not found");
			}
			$answer[$key_i] = unserialize($answer[$key_i]);
		}
		return is_array($key) ? $answer : reset($answer);
	}

	public function Put(string $key, $value, int $ttl = null, array $tags = []) {
		$params = (is_null($ttl)) ? [] : ['nx', 'ex' => $ttl];
		$result = $this->Redis->set("val|{$key}", serialize($value), $params);
		if ($result === false) {
			throw new ExceptionCache("Key exist: '{$key}'");
		}
		if (!empty($tags)) {
			foreach ($tags as $tag) {
				$this->Redis->sAdd("key|{$key}", $tag);
				$this->Redis->sAdd("tag|{$tag}", $key);
			}
		}
	}

	public function Delete(string $key) {
		$this->Redis->del("val|{$key}");
		if ($this->Redis->exists("key|{$key}")) {
			$tags = $this->Redis->sMembers("key|{$key}");
			$this->Redis->del("key|{$key}");
			foreach ($tags as $tag) {
				$this->Redis->sRem("tag|{$tag}", $key);
			}
		}
	}

	public function Strike($tags) {
		$tags = is_array($tags) ? $tags : [$tags];
		foreach ($tags as $tag) {
			if ($this->Redis->exists("tag|{$tag}")) {
				$keys = $this->Redis->sMembers("tag|{$tag}");
				$this->Redis->del("tag|{$tag}");
				foreach ($keys as $key) {
					$this->Delete($key);
				}
			}
		}
	}

	public function Clear() {
		$this->Redis->flushDB();
	}
}