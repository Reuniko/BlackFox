<?php

namespace BlackFox;

class MySQL extends Database {

	private $link;

	public function Init(array $params) {

		$this->database = $params['DATABASE'];

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
			return null;
		}
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

	public function QuerySingleInsert($SQL, $increment = null) {
		$result = mysqli_query($this->link, $SQL);
		if ($result === false) {
			throw new ExceptionSQL(mysqli_error($this->link), $SQL);
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

	public function Random() {
		return 'rand()';
	}

	public function GetStructureStringType(array $field) {
		if (empty($field['TYPE'])) {
			throw new ExceptionType("Empty data type");
		}
		switch ($field['TYPE']) {
			case 'STRING':
			case 'PASSWORD':
				return "varchar(" . ((int)$field['LENGTH'] ?: 255) . ")";
				break;
			case 'ARRAY':
			case 'TEXT':
			case 'LIST':
				return "text";
			case 'BOOLEAN':
				return "bool";
			case 'INTEGER':
			case 'OUTER':
			case 'FILE':
				return "int";
			case 'FLOAT':
				$length = $field['LENGTH'] ?: 13;
				$decimals = $field['DECIMALS'] ?: 2;
				return "float({$length},{$decimals})";
			case 'INNER':
				throw new ExceptionType("No fields required");
			case 'TIME':
				return "time";
			case 'DATE':
				return "date";
			case 'DATETIME':
				return "datetime";
			case 'ENUM':
				return 'enum' . '("' . implode('", "', array_keys($field['VALUES'])) . '")';
			case 'SET':
				return 'set' . '("' . implode('", "', array_keys($field['VALUES'])) . '")';
			default:
				throw new ExceptionType("Unknown data type: " . $field['TYPE']);
		}
	}

	public function GetStructureString(array $field) {
		$type = $this->GetStructureStringType($field);

		$null = ($field["NOT_NULL"] || $field['PRIMARY']) ? "NOT NULL" : "NULL";

		$default = "";
		if (isset($field['DEFAULT'])) {
			if (is_bool($field['DEFAULT'])) {
				$default = "DEFAULT " . ($field['DEFAULT'] ? 'true' : 'false');
			} elseif (is_array($field['DEFAULT'])) {
				$default = "DEFAULT '" . implode(',', $field['DEFAULT']) . "'";
			} elseif (is_string($field['DEFAULT'])) {
				$default = "DEFAULT '{$field['DEFAULT']}'";
			} elseif (is_numeric($field['DEFAULT'])) {
				$default = "DEFAULT {$field['DEFAULT']}";
			} else {
				throw new Exception("Unknown default value type of '{$field['CODE']}'");
			}
		}

		$auto_increment = ($field["AUTO_INCREMENT"]) ? "AUTO_INCREMENT" : "";

		$comment = ($field["NAME"]) ? " COMMENT '{$field["NAME"]}'" : "";

		$structure_string = $this->Quote($field['CODE']) . " $type $null $default $auto_increment $comment";

		return $structure_string;
	}

	private function GetConstraints($table) {
		$this->Query("
			SELECT 
			KEY_COLUMN_USAGE.CONSTRAINT_NAME,
			KEY_COLUMN_USAGE.COLUMN_NAME, 
			REFERENTIAL_CONSTRAINTS.UPDATE_RULE,
			REFERENTIAL_CONSTRAINTS.DELETE_RULE,
			-1 FROM information_schema.KEY_COLUMN_USAGE
			INNER JOIN information_schema.REFERENTIAL_CONSTRAINTS
				ON REFERENTIAL_CONSTRAINTS.CONSTRAINT_NAME = KEY_COLUMN_USAGE.CONSTRAINT_NAME
			WHERE KEY_COLUMN_USAGE.TABLE_SCHEMA = '{$this->database}'
			AND KEY_COLUMN_USAGE.TABLE_NAME = '{$table}'
		", 'COLUMN_NAME');
	}

	public function DropTableConstraints($table) {
		$db_constraints = $this->GetConstraints($table);
		if (!empty($db_constraints))
			foreach ($db_constraints as $db_constraint)
				$this->Query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$db_constraint['CONSTRAINT_NAME']}`");
	}

	public function SynchronizeTable($table, $fields) {
		$strict = true;
		if (empty($fields)) {
			throw new Exception("Synchronize of '{$table}' failed: fields is empty");
		}
		$tables = $this->Query("SHOW TABLES LIKE '{$table}'");
		$fields = array_change_key_case($fields, CASE_UPPER);

		$rows = [];
		$keys = [];

		if (empty($tables)) {
			foreach ($fields as $code => $Info) {
				/** @var Type $Info */
				if ($Info['PRIMARY']) {
					$keys[] = $code;
				}
				try {
					$rows[] = $this->GetStructureString($Info);
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
			foreach ($fields as $code => $Info) {
				/** @var Type $Info */
				if ($Info['PRIMARY']) {
					$keys[] = $code;
				}
				try {
					$structure_string = $this->GetStructureString($Info);
				} catch (\Exception $error) {
					continue;
				}
				if ($strict && !empty($last_after_code)) {
					$structure_string .= " AFTER " . $this->Quote($last_after_code);
				}
				if (!empty($columns[$code])) {
					$rows[] = "MODIFY COLUMN $structure_string";
				} elseif (!empty($Info['CHANGE']) && !empty($columns[$Info['CHANGE']])) {
					$rows[] = "CHANGE COLUMN `{$Info['CHANGE']}` $structure_string";
					unset($columns[$Info['CHANGE']]);
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

		// INDEXES:
		$db_indexes = $this->Query("SHOW INDEX FROM `{$table}`", 'Column_name');
		foreach ($fields as $code => $Info) {
			/** @var Type $Info */
			if (in_array($code, $keys)) {
				continue;
			}
			if ($Info['FOREIGN']) {
				$Info['INDEX'] = true;
			}
			if ($Info['UNIQUE']) {
				$Info['INDEX'] = true;
			}
			if ($Info['INDEX'] === 'UNIQUE') {
				$Info['INDEX'] = true;
				$Info['UNIQUE'] = true;
			}
			$unique = ($Info['UNIQUE']) ? 'UNIQUE' : '';
			$db_index = $db_indexes[$code];

			// index is: present in database, missing in code - drop it
			if (isset($db_index) and !$Info['INDEX']) {
				$this->Query("ALTER TABLE `{$table}` DROP INDEX `{$code}`;");
				continue;
			}

			// index is: missing in database, present in code - create it
			if ($Info['INDEX'] and !isset($db_index)) {
				$this->Query("ALTER TABLE `{$table}` ADD {$unique} INDEX `{$code}` (`{$code}`);");
				continue;
			}

			// index is: present in database, present in code - check unique
			if (isset($db_index)) {
				if (($Info['UNIQUE'] and $db_index['Non_unique']) or (!$Info['UNIQUE'] and !$db_index['Non_unique'])) {
					$this->Query("ALTER TABLE `{$table}` DROP INDEX `{$code}`, ADD {$unique} INDEX `{$code}` (`{$code}`);");
					continue;
				}
			}
		}

	}

	public function CreateTableConstraints($table, $fields) {
		foreach ($fields as $code => $field) {
			if (!isset($field['FOREIGN'])) {
				continue;
			}
			$action = is_string($field['FOREIGN']) ? $field['FOREIGN'] : 'RESTRICT';
			/** @var SCRUD $Link */
			$Link = $field['LINK']::I();
			$link_key = $field['INNER_KEY'] ?: $Link->key();
			$this->Query("ALTER TABLE `{$table}` ADD FOREIGN KEY (`{$code}`) REFERENCES `{$Link->code}` (`{$link_key}`) ON DELETE {$action} ON UPDATE {$action}");
		}
	}

	public function CompileSQLSelect(array $parts) {
		$SQL = [];
		$SQL[] = 'SELECT';
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