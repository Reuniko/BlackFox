<?php

namespace System;

class DatabaseDriverMySQL extends Database {

	/**
	 * @var \mysqli
	 */
	private $link;

	public function __construct($params = []) {

		$this->link = mysqli_connect(
			$params['HOST'],
			$params['USER'],
			$params['PASSWORD'],
			$params['DATABASE'],
			$params['PORT']
		);

		if ($this->link === false) {
			throw new Exception(mysqli_connect_error());
		}
		mysqli_set_charset($this->link, $params['CHARSET'] ?: 'utf8');
	}

	public function Query($SQL, $key = null) {
		$result = mysqli_query($this->link, $SQL);
		if ($result === false) {
			throw new ExceptionSQL(mysqli_error($this->link), $SQL);
		}
		if ($result === true) {
			return mysqli_insert_id($this->link) ?: true;
		}
		if (is_object($result)) {
			$data = [];
			while ($row = mysqli_fetch_assoc($result)) {
				if (isset($key) and isset($row[$key])) {
					$data[$row[$key]] = $row;
				} else {
					$data[] = $row;
				}
			}
			return $data;
		}
	}

	public function QuerySingleInsert($SQL, $increment = null) {
		$result = mysqli_query($this->link, $SQL);
		if ($result === false) {
			throw new ExceptionSQL(mysqli_error($this->link) . $SQL, $SQL);
		}
		if ($result === true) {
			return mysqli_insert_id($this->link);
		}
	}

	public function Escape($data) {
		if (is_null($data)) {
			return null;
		}
		return mysqli_real_escape_string($this->link, $data);
	}

	public function Quote($id) {
		return '`' . $id . '`';
	}

	public function SynchronizeTable($table, $structure) {
		$strict = true;
		if (empty($structure)) {
			throw new Exception("Synchronize of '{$table}' failed: structure is empty");
		}
		$tables = $this->Query("SHOW TABLES LIKE '{$table}'");
		$structure = array_change_key_case($structure, CASE_UPPER);

		$rows = [];
		$keys = [];

		if (empty($tables)) {
			foreach ($structure as $code => $Type) {
				/** @var Type $Type */
				if ($Type['PRIMARY']) {
					$keys[] = $code;
				}
				try {
					$rows[] = $this->GetStructureString($Type);
				} catch (\Exception $error) {
					continue;
				}
			}
			if (!empty($keys)) {
				$rows[] = "PRIMARY KEY (" . implode(", ", $keys) . ")";
			}
			$SQL = "CREATE TABLE `{$table}` \r\n" . "(" . implode(",\r\n", $rows) . ");";
			$this->Query($SQL);
		} else {
			$columns = $this->Query("SHOW FULL COLUMNS FROM " . $table, 'Field');
			$columns = array_change_key_case($columns, CASE_UPPER);

			$db_keys = [];
			foreach ($columns as $code => $column) {
				if ($column['Key'] === 'PRI') {
					$db_keys[] = $code;
				}
			}

			$last_after_code = '';
			foreach ($structure as $code => $Type) {
				/** @var Type $Type */
				if ($Type['PRIMARY']) {
					$keys[] = $code;
				}
				try {
					$structure_string = $this->GetStructureString($Type);
				} catch (\Exception $error) {
					continue;
				}
				if ($strict && !empty($last_after_code)) {
					$structure_string .= " AFTER {$last_after_code}";
				}
				if (!empty($columns[$code])) {
					$rows[] = "MODIFY COLUMN $structure_string";
				} elseif (!empty($Type['CHANGE']) && !empty($columns[$Type['CHANGE']])) {
					$rows[] = "CHANGE COLUMN `{$Type['CHANGE']}` $structure_string";
					unset($columns[$Type['CHANGE']]);
				} else {
					$rows[] = "ADD COLUMN $structure_string";
				}
				$last_after_code = $code;
				unset($columns[$code]);
			}
			if ($strict) {
				foreach ($columns as $code => $column) {
					$rows[] = "DROP COLUMN `{$code}`";
				}
			}
			if (!empty($keys) and ($keys <> $db_keys)) {
				if (!empty($db_keys)) {
					$rows[] = "DROP PRIMARY KEY";
				}
				$rows[] = "ADD PRIMARY KEY (" . implode(", ", array_map([$this, 'Quote'], $keys)) . ")";
			}
			$SQL = "ALTER TABLE `{$table}` \r\n" . implode(",\r\n", $rows) . ";";
			$this->Query($SQL);
		}

		$indexes = $this->Query("SHOW INDEX FROM `{$table}`", 'Column_name');
		foreach ($structure as $code => $Type) {
			if (in_array($code, $keys)) {
				continue;
			}
			if ($Type['UNIQUE']) {
				$Type['INDEX'] = true;
			}
			if ($Type['INDEX'] === 'UNIQUE') {
				$Type['INDEX'] = true;
				$Type['UNIQUE'] = true;
			}
			$unique = ($Type['UNIQUE']) ? 'UNIQUE' : '';
			$index = $indexes[$code];

			// в базе есть, в коде нет - удалить
			if (isset($index) && !$Type['INDEX']) {
				$this->Query("ALTER TABLE `{$table}` DROP INDEX `{$code}`;");
				continue;
			}

			// в базе нет, в коде есть - добавить
			if (($Type['INDEX']) && (!isset($index))) {
				$this->Query("ALTER TABLE `{$table}` ADD {$unique} INDEX `{$code}` (`{$code}`);");
				continue;
			}

			// в базе есть, в коде есть - уточнение уникальности индекса
			if (isset($index)) {
				if (($Type['UNIQUE'] && $index['Non_unique']) || (!$Type['UNIQUE'] && !$index['Non_unique'])) {
					$this->Query("ALTER TABLE `{$table}` DROP INDEX `{$code}`, ADD {$unique} INDEX `{$code}` (`{$code}`);");
					continue;
				}
			}
		}
	}

	public function GetStructureString(Type $Type) {
		$type = $Type->GetStructureStringType();

		$null = ($Type["NOT_NULL"] || $Type['PRIMARY']) ? "NOT NULL" : "NULL";

		$default = "";
		if ($Type['DEFAULT']) {
			if (is_array($Type['DEFAULT'])) {
				$Type['DEFAULT'] = implode(',', $Type['DEFAULT']);
			}
			$default = "DEFAULT '{$Type['DEFAULT']}'";
		}

		$auto_increment = ($Type["AUTO_INCREMENT"]) ? "AUTO_INCREMENT" : "";

		$comment = ($Type["NAME"]) ? " COMMENT '{$Type["NAME"]}'" : "";

		$structure_string = $this->Quote($Type['CODE']) . " $type $null $default $auto_increment $comment";

		return $structure_string;
	}

	public function CompileSQLSelect(array $parts) {
		$SQL = [];
		$SQL[] = 'SELECT';
		if ($parts['LIMIT']) $SQL[] = 'SQL_CALC_FOUND_ROWS';
		$SQL[] = implode(",\r\n", $parts['SELECT']);
		$SQL[] = "FROM {$parts['TABLE']}";
		$SQL[] = implode("\r\n", $parts['JOIN']);
		if ($parts['WHERE']) $SQL[] = "WHERE " . implode("\r\nAND ", $parts['WHERE']);
		if ($parts['GROUP']) $SQL[] = "GROUP BY " . implode(", ", $parts['GROUP']);
		if ($parts['ORDER']) $SQL[] = "ORDER BY " . implode(", ", $parts['ORDER']);
		if ($parts['LIMIT']) $SQL[] = "LIMIT {$parts['LIMIT']['FROM']}, {$parts['LIMIT']['COUNT']}";
		return implode("\r\n", $SQL);
	}
}