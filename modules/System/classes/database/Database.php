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

	abstract public function QuerySingleInsert($SQL, $increment = null);

	/**
	 * Process data escape before insert it into SQL query
	 *
	 * @param string $data
	 * @return string
	 */
	abstract public function Escape($data);

	abstract public function Quote($data);

	abstract public function Random();

	public function StartTransaction() {
		$this->Query('START TRANSACTION');
	}

	public function Rollback() {
		$this->Query('ROLLBACK');
	}

	public function Commit() {
		$this->Query('COMMIT');
	}

	/**
	 * @param $table
	 * @param $structure
	 * @return mixed
	 * @throws Exception
	 */
	abstract public function SynchronizeTable($table, $structure);

	abstract public function CompileSQLSelect(array $parts);
}