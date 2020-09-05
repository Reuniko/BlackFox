<?php

namespace BlackFox;

abstract class Database {

	use Instance;

	public $database;

	/**
	 * Execute SQL query and return a result as array
	 *
	 * @param string $SQL SQL statement
	 * @param string $key optional, indicates which column to form array keys
	 * @return mixed
	 * @throws ExceptionSQL
	 */
	abstract public function Query($SQL, $key = null);

	/**
	 * Execute SQL query and return a result as value of ID, which has been inserted
	 *
	 * @param string $SQL
	 * @param string $increment
	 * @return int|string
	 * @throws ExceptionSQL
	 */
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

	abstract public function CompareTable(SCRUD $Table);

	abstract public function CompileSQLSelect(array $parts);

	public function Truncate($table) {
		$this->Query("TRUNCATE TABLE {$table}");
	}

	public function Drop($table) {
		$this->Query("DROP TABLE IF EXISTS {$table}");
	}

	/** @var array */
	public $db_types = [];

	public function GetStructureStringType(Type $Type) {
		if (empty($Type->db_type))
			throw new Exception("Empty db_type in Type: " . get_class($Type));
		if (!isset($this->db_types[$Type->db_type]))
			throw new Exception("Unknown db_type: " . $Type->db_type);

		$db_type = $this->db_types[$Type->db_type];
		$string = $db_type['type'];
		if (is_callable($db_type['getParams'])) {
			$params = $db_type['getParams']($Type->field);
			$string .= '(' . implode(',', $params) . ')';
		}
		return $string;
	}
}