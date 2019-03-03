<?php

namespace System;

abstract class Database extends Instanceable {

	abstract function __construct($params);

	/**
	 * Execute SQL query and return a result as array or value of ID, which has been inserted
	 *
	 * @param string $SQL
	 * @param string $key
	 * @return mixed
	 */
	abstract public function Query($SQL, $key = null);

	/**
	 * Process data escape before insert it into SQL query
	 *
	 * @param string $data
	 * @return string
	 */
	abstract public function Escape($data);

	public function StartTransaction() {
		$this->Query('START TRANSACTION');
	}

	public function Rollback() {
		$this->Query('ROLLBACK');
	}

	public function Commit() {
		$this->Query('COMMIT');
	}
}