<?php

namespace System;

class Cache extends Instanceable {

	public function Get(string $key) {
		throw new ExceptionCache('Base cache driver needs to be replaced with any working child');
	}

	public function Set(string $key, $value, int $ttl = null, array $tags = []) {
		throw new ExceptionCache('Base cache driver needs to be replaced with any working child');
	}

	public function Clean(string $key) {
		throw new ExceptionCache('Base cache driver needs to be replaced with any working child');
	}

	public function Strike($tags) {
		throw new ExceptionCache('Base cache driver needs to be replaced with any working child');
	}

	public function Wipe() {
		throw new ExceptionCache('Base cache driver needs to be replaced with any working child');
	}
}