<?php

namespace BlackFox;

class MySQL extends Database {

	private $link;

	public function __construct(array $params) {

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
				return "tinyint(1)";
			case 'INTEGER':
			case 'OUTER':
			case 'FILE':
				return "int(" . ((int)$field['LENGTH'] ?: 11) . ")";
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
				return 'enum' . '(\'' . implode('\',\'', array_keys($field['VALUES'])) . '\')';
			case 'SET':
				return 'set' . '(\'' . implode('\',\'', array_keys($field['VALUES'])) . '\')';
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
		$structure_string = preg_replace('/\s+/', ' ', $structure_string);

		return $structure_string;
	}

	private function GetConstraints($table) {
		return $this->Query("
			SELECT 
			KEY_COLUMN_USAGE.CONSTRAINT_NAME, 
			KEY_COLUMN_USAGE.COLUMN_NAME, 
			KEY_COLUMN_USAGE.REFERENCED_TABLE_NAME, 
			KEY_COLUMN_USAGE.REFERENCED_COLUMN_NAME, 
			REFERENTIAL_CONSTRAINTS.UPDATE_RULE,
			REFERENTIAL_CONSTRAINTS.DELETE_RULE
			FROM information_schema.KEY_COLUMN_USAGE
			INNER JOIN information_schema.REFERENTIAL_CONSTRAINTS
				ON REFERENTIAL_CONSTRAINTS.CONSTRAINT_NAME = KEY_COLUMN_USAGE.CONSTRAINT_NAME
				AND REFERENTIAL_CONSTRAINTS.CONSTRAINT_SCHEMA = KEY_COLUMN_USAGE.TABLE_SCHEMA
			WHERE KEY_COLUMN_USAGE.TABLE_SCHEMA = '{$this->database}'
			AND KEY_COLUMN_USAGE.TABLE_NAME = '{$table}'
		", 'CONSTRAINT_NAME');
	}

	public function CompareTable(SCRUD $Table) {
		$diff = [];
		$diff = array_merge($diff, $this->CompareTableFieldsAndPrimaryKeys($Table));
		$diff = array_merge($diff, $this->CompareTableIndexes($Table));
		$diff = array_merge($diff, $this->CompareTableConstraints($Table));
		return $diff;
	}

	public function CompareTableFieldsAndPrimaryKeys(SCRUD $Table) {
		if (empty($Table->fields))
			throw new Exception("Can't compare table fields: no fields found, table '{$Table->name}' [{$Table->code}]");

		$diff = [];
		$check = $this->Query("SHOW TABLES LIKE '{$Table->code}'");

		if (empty($check)) {
			// no table found: creating a new one with fields and primary keys
			$data = [];
			foreach ($Table->fields as $code => $field) {
				if ($Table->Types[$code]->virtual)
					continue;
				$data[] = [
					'MESSAGE' => 'Add column',
					'FIELD'   => $code,
					'SQL'     => $this->GetStructureString($field),
				];
			}
			if (!empty($Table->keys)) {
				$data[] = [
					'MESSAGE' => 'Add primary keys',
					'SQL'     => "PRIMARY KEY (" . implode(", ", $Table->keys) . ")",
				];
			}
			$SQL = "CREATE TABLE `{$Table->code}` (\r\n" . implode(",\r\n", array_column($data, 'SQL')) . "\r\n);";
			$diff[] = [
				'MESSAGE' => 'Create a new table',
				'TABLE'   => $Table->code,
				'SQL'     => $SQL,
				'DATA'    => $data,
			];
		} else {
			// table exist: comparing fields and primary keys
			$data = [];
			$columns = $this->Query("SHOW FULL COLUMNS FROM " . $Table->code, 'Field');
			$columns = array_change_key_case($columns, CASE_UPPER);

			$db_keys = [];
			foreach ($columns as $code => $column)
				if ($column['Key'] === 'PRI')
					$db_keys[] = $code;

			$last_after_code = '';
			foreach ($Table->fields as $code => $field) {
				if ($field['PRIMARY']) {
					$keys[] = $code;
				}

				if ($Table->Types[$code]->virtual)
					continue;
				$structure_string = $this->GetStructureString($field);

				if (!empty($last_after_code))
					$structure_string .= " AFTER " . $this->Quote($last_after_code);

				if (!empty($columns[$code])) {
					$reason = $this->IsFieldDifferentFromColumn($field, $columns[$code]);
					if ($reason) {
						$data[] = [
							'MESSAGE' => 'Modify column',
							'FIELD'   => $code,
							'REASON'  => $reason,
							'SQL'     => "MODIFY COLUMN $structure_string",
						];
					}
				} elseif (!empty($field['CHANGE']) && !empty($columns[$field['CHANGE']])) {
					$data[] = [
						'MESSAGE' => 'Rename column',
						'FIELD'   => $code,
						'SQL'     => "CHANGE COLUMN `{$field['CHANGE']}` $structure_string",
					];
					unset($columns[$field['CHANGE']]);
				} else {
					$data[] = [
						'MESSAGE' => 'Add column',
						'FIELD'   => $code,
						'SQL'     => "ADD COLUMN $structure_string",
					];
				}
				$last_after_code = $code;
				unset($columns[$code]);
			}
			foreach ($columns as $code => $column) {
				$data[] = [
					'MESSAGE' => 'Drop column',
					'FIELD'   => $code,
					'SQL'     => "DROP COLUMN `{$code}`",
				];
			}

			if ($Table->keys <> $db_keys) {
				if (!empty($Table->keys)) {
					$data[] = [
						'MESSAGE' => 'Modify primary keys',
						'SQL'     => "DROP PRIMARY KEY, ADD PRIMARY KEY (" . implode(", ", array_map([$this, 'Quote'], $Table->keys)) . ")",
					];
				} else {
					$data[] = [
						'MESSAGE' => 'Drop primary keys',
						'SQL'     => "DROP PRIMARY KEY",
					];
				}
			}

			if (!empty($data)) {
				$SQL = "ALTER TABLE `{$Table->code}` \r\n" . implode(",\r\n", array_column($data, 'SQL'));
				$diff[] = [
					'MESSAGE' => 'Modify table',
					'TABLE'   => $Table->code,
					'DATA'    => $data,
					'SQL'     => $SQL,
				];
			}
		}
		return $diff;
	}

	public function CompareTableIndexes(SCRUD $Table) {
		$diff = [];

		try {
			$db_indexes = $this->Query("SHOW INDEX FROM `{$Table->code}`", 'Column_name');
		} catch (\Exception $error) {
			$db_indexes = [];
		}

		foreach ($Table->fields as $code => $field) {

			if ($field['PRIMARY'])
				continue; // skip primary keys

			if ($field['FOREIGN']) {
				$field['INDEX'] = true;
			}
			if ($field['UNIQUE']) {
				$field['INDEX'] = true;
			}
			if ($field['INDEX'] === 'UNIQUE') {
				$field['INDEX'] = true;
				$field['UNIQUE'] = true;
			}

			$unique = ($field['UNIQUE']) ? 'UNIQUE' : '';
			$db_index = $db_indexes[$code];

			// index is: present in database, missing in code - drop it
			if (isset($db_index) and !$field['INDEX']) {
				$diff[] = [
					'MESSAGE'  => 'Drop index',
					'TABLE'    => $Table->code,
					'FIELD'    => $code,
					'PRIORITY' => -1,
					'SQL'      => "ALTER TABLE `{$Table->code}` DROP INDEX `{$db_index['Key_name']}`",
				];
				continue;
			}

			// index is: missing in database, present in code - create it
			if ($field['INDEX'] and !isset($db_index)) {
				$diff[] = [
					'MESSAGE'  => 'Add index',
					'TABLE'    => $Table->code,
					'FIELD'    => $code,
					'PRIORITY' => 1,
					'SQL'      => "ALTER TABLE `{$Table->code}` ADD {$unique} INDEX `{$code}` (`{$code}`)",
				];
				continue;
			}

			// index is: present in database, present in code - check unique
			if (isset($db_index)) {
				if (($field['UNIQUE'] and $db_index['Non_unique']) or (!$field['UNIQUE'] and !$db_index['Non_unique'])) {
					$diff[] = [
						'MESSAGE'  => 'Modify index (drop)',
						'TABLE'    => $Table->code,
						'FIELD'    => $code,
						'PRIORITY' => -1,
						'SQL'      => "ALTER TABLE `{$Table->code}` DROP INDEX `{$db_index['Key_name']}`",
					];
					$diff[] = [
						'MESSAGE'  => 'Modify index (add)',
						'TABLE'    => $Table->code,
						'FIELD'    => $code,
						'PRIORITY' => 1,
						'SQL'      => "ALTER TABLE `{$Table->code}` ADD {$unique} INDEX `{$code}` (`{$code}`)",
					];
					continue;
				}
			}

		}
		return $diff;
	}

	public function CompareTableConstraints(SCRUD $Table) {
		$diff = [];
		$db_constraints = $this->GetConstraints($Table->code);

		foreach ($Table->fields as $code => $field) {
			if (!isset($field['FOREIGN']))
				continue;

			$action = is_string($field['FOREIGN']) ? $field['FOREIGN'] : 'RESTRICT';
			/** @var SCRUD $Link */
			$Link = $field['LINK']::I();
			$link_key = $field['INNER_KEY'] ?: $Link->key();

			$fkey = "fkey {$Table->code}.{$code} ref {$Link->code}.{$link_key}";

			if (!isset($db_constraints[$fkey])) {
				$diff[] = [
					'MESSAGE'  => 'Add constraint',
					'PRIORITY' => 2,
					'TABLE'    => $Table->code,
					'FIELD'    => $code,
					'SQL'      => "ALTER TABLE `{$Table->code}` ADD CONSTRAINT `{$fkey}` \r\n FOREIGN KEY (`{$code}`) REFERENCES `{$Link->code}` (`{$link_key}`) ON DELETE {$action} ON UPDATE {$action}",
				];
				continue;
			}

			$constraint = $db_constraints[$fkey];

			$changed = false;
			$changed |= $constraint['COLUMN_NAME'] <> $code;
			$changed |= $constraint['REFERENCED_TABLE_NAME'] <> $Link->code;
			$changed |= $constraint['REFERENCED_COLUMN_NAME'] <> $link_key;
			$changed |= $constraint['UPDATE_RULE'] <> $action;
			$changed |= $constraint['DELETE_RULE'] <> $action;

			if ($changed) {
				$diff[] = [
					'MESSAGE'  => 'Alter constraint (drop)',
					'PRIORITY' => -2,
					'TABLE'    => $Table->code,
					'FIELD'    => $code,
					'SQL'      => "ALTER TABLE `{$Table->code}` DROP CONSTRAINT `{$fkey}`",
				];
				$diff[] = [
					'MESSAGE'  => 'Alter constraint (add)',
					'PRIORITY' => 2,
					'TABLE'    => $Table->code,
					'FIELD'    => $code,
					'SQL'      => "ALTER TABLE `{$Table->code}` ADD CONSTRAINT `{$fkey}` \r\n FOREIGN KEY (`{$code}`) REFERENCES `{$Link->code}` (`{$link_key}`) ON DELETE {$action} ON UPDATE {$action}",
				];
			}

			unset($db_constraints[$fkey]);
		}

		foreach ($db_constraints as $db_constraint) {
			$diff[] = [
				'MESSAGE'  => 'Drop constraint',
				'PRIORITY' => -2,
				'TABLE'    => $Table->code,
				'FIELD'    => $code,
				'SQL'      => "ALTER TABLE `{$Table->code}` DROP FOREIGN KEY `{$db_constraint['CONSTRAINT_NAME']}`",
			];
		}

		return $diff;
	}

	public function IsFieldDifferentFromColumn(array $field, array $column) {
		// type
		$type = $this->GetStructureStringType($field);
		if ($type <> $column['Type'])
			return "Change type: {$column['Type']} -> {$type}";

		// not null
		if ($field['NOT_NULL'] and $column['Null'] == 'YES')
			return 'NULL -> NOT_NULL';
		if (!$field['NOT_NULL'] and $column['Null'] <> 'YES')
			return 'NOT_NULL -> NULL';

		// auto increment
		if ($field['AUTO_INCREMENT'] and false === strpos($column['Extra'], 'auto_increment'))
			return 'Add auto increment';

		// default
		$default = $field['DEFAULT'];
		if (is_array($default))
			$default = implode(',', $default);
		if ($default <> $column['Default'])
			return 'Change default value';

		return false;
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