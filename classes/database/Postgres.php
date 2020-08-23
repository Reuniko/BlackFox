<?php

namespace BlackFox;

class Postgres extends Database {

	private $link;

	public function __construct(array $params) {

		$this->database = $params['DATABASE'];

		$connection_string = [
			"host={$params['HOST']}",
			"port={$params['PORT']}",
			"dbname={$params['DATABASE']}",
			"user={$params['USER']}",
			"password={$params['PASSWORD']}",
		];

		$this->link = pg_pconnect(implode(' ', $connection_string));

		if ($this->link === false) {
			throw new Exception(pg_last_error());
		}
	}

	public function QuerySingleInsert($SQL, $increment = null) {
		if (!empty($increment)) {
			$SQL .= " RETURNING " . $this->Quote($increment);
		}
		$result = $this->Query($SQL);
		if (empty($increment) or empty($result)) {
			return null;
		}
		return reset($result)[$increment];
	}

	public function Query($SQL, $key = null) {
		@$result = pg_query($this->link, $SQL);
		if ($result === false) {
			throw new ExceptionSQL(pg_last_error($this->link), $SQL);
		}
		$data = [];
		while ($row = pg_fetch_assoc($result)) {
			if (isset($key) and isset($row[$key])) {
				$data[$row[$key]] = $row;
			} else {
				$data[] = $row;
			}
		}
		return $data;
	}

	public function Escape($data) {
		if (is_null($data)) {
			return null;
		}
		return pg_escape_string($this->link, $data);
	}

	public function Quote($id) {
		return '"' . $id . '"';
	}

	public function Random() {
		return 'random()';
	}

	public function GetStructureStringType(array $field) {
		if (empty($field['TYPE'])) {
			throw new ExceptionType("Empty data type");
		}
		switch ($field['TYPE']) {
			case 'STRING':
			case 'ENUM':
			case 'SET':
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
				return "int4";
			case 'FLOAT':
				$length = $field['LENGTH'] ?: 13;
				$decimals = $field['DECIMALS'] ?: 2;
				return "numeric({$length},{$decimals})";
			case 'INNER':
				throw new ExceptionType("No fields required");
			case 'TIME':
				return "time";
			case 'DATE':
				return "date";
			case 'DATETIME':
				return "timestamp";
			default:
				throw new ExceptionType("Unknown data type: " . $field['TYPE']);
		}
	}

	private function GetConstraints($table) {
		return $this->Query("
			SELECT
		    tc.table_schema, 
		    tc.constraint_name, 
		    tc.table_name, 
		    kcu.column_name, 
		    ccu.table_schema AS foreign_table_schema,
		    ccu.table_name AS foreign_table_name,
		    ccu.column_name AS foreign_column_name,
			rc.update_rule,
			rc.delete_rule,
			-1 FROM 
		    information_schema.table_constraints AS tc 
		    JOIN information_schema.key_column_usage AS kcu
		      ON tc.constraint_name = kcu.constraint_name
		      AND tc.table_schema = kcu.table_schema
		    JOIN information_schema.constraint_column_usage AS ccu
		      ON ccu.constraint_name = tc.constraint_name
		      AND ccu.table_schema = tc.table_schema
			JOIN information_schema.referential_constraints AS rc
			  ON rc.constraint_name =  tc.constraint_name
		WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_name='{$table}';
		", 'column_name');
	}

	public function DropTableConstraints($table) {
		$db_constraints = $this->GetConstraints($table);
		if (!empty($db_constraints))
			foreach ($db_constraints as $db_constraint)
				$this->Query("ALTER TABLE \"{$table}\" DROP CONSTRAINT \"{$db_constraint['constraint_name']}\"");
	}


	public function CompareTable(SCRUD $Table) {
		$diff = [];
		$diff = array_merge($diff, $this->CompareTableFieldsAndPrimaryKeys($Table));
//		$diff = array_merge($diff, $this->CompareTableIndexes($Table));
//		$diff = array_merge($diff, $this->CompareTableConstraints($Table));
		return $diff;
	}

	public function CompareTableFieldsAndPrimaryKeys(SCRUD $Table) {

		$diff = [];
		$check = $this->Query("SELECT * FROM pg_catalog.pg_tables WHERE tablename='{$Table->code}'");

		if (empty($check)) {
			// no table found: creating a new one with fields and primary keys
			$data = [];
			foreach ($Table->fields as $code => $field) {
				if ($Table->Types[$code]->virtual)
					continue;
				$type = $this->GetStructureStringType($field);
				$null = ($field["NOT_NULL"] || $field['PRIMARY']) ? "NOT NULL" : "NULL";
				$default = "";
				if (isset($field['DEFAULT'])) {
					$default = !is_array($field['DEFAULT']) ? $field['DEFAULT'] : implode(',', $field['DEFAULT']);
					$default = "DEFAULT '{$default}'";
				}
				if ($field["AUTO_INCREMENT"]) {
					$type = 'serial';
				}
				$data[] = [
					'MESSAGE' => 'Add column',
					'FIELD'   => $code,
					'SQL'     => $this->Quote($field['CODE']) . " $type $null $default",
				];
			}
			if (!empty($Table->keys)) {
				$data[] = [
					'MESSAGE' => 'Add primary keys',
					'SQL'     => "PRIMARY KEY (" . implode(", ", array_map([$this, 'Quote'], $Table->keys)) . ")",
				];
			}
			$SQL = "CREATE TABLE {$Table->code} (\r\n" . implode(",\r\n", array_column($data, 'SQL')) . "\r\n);";
			$diff[] = [
				'MESSAGE' => 'Create a new table',
				'TABLE'   => $Table->code,
				'SQL'     => $SQL,
				'DATA'    => $data,
			];
		} else {
			// table exist: comparing fields and primary keys
			$data = [];
			$columns = $this->Query("SELECT * FROM information_schema.columns WHERE table_catalog='{$this->database}' AND table_name ='{$Table->code}' ORDER BY ordinal_position;", 'column_name');
			$columns = array_change_key_case($columns, CASE_UPPER);

			debug($columns, '$columns');

			$rows = [];
			$keys = [];
			$renames = [];

			foreach ($Table->fields as $code => $field) {
				$code_quoted = $this->Quote($code);
				if ($field['PRIMARY']) {
					$keys[] = $code;
				}

				// renames
				if ($field['CHANGE'] and !empty($columns[$field['CHANGE']])) {
					$renames[] = "RENAME \"{$field['CHANGE']}\" TO {$code_quoted}";
					$columns[$code] = $columns[$field['CHANGE']];
					unset($columns[$field['CHANGE']]);
				}

				// type
				if ($Table->Types[$code]->virtual)
					continue;
				$type = $this->GetStructureStringType($field);

				// ADD COLUMN:
				if (!isset($columns[$code])) {
					if ($field["AUTO_INCREMENT"]) {
						$type = 'serial';
					}
					$default = '';
					if (isset($field['DEFAULT'])) {
						if (is_bool($field['DEFAULT'])) {
							$default = "DEFAULT " . ($field['DEFAULT'] ? 'true' : 'false');
						} else {
							$default = "DEFAULT '{$field['DEFAULT']}'";
						}
					}
					$not_null = $field['NOT_NULL'] ? 'NOT NULL' : '';

					$data[] = [
						'MESSAGE' => 'Add column',
						'FIELD'   => $code,
						'SQL'     => "ADD COLUMN {$code_quoted} {$type} {$default} {$not_null}",
					];

					unset($columns[$code]);
					continue;
				}

				// ALTER COLUMN:
				// todo не делать лишней работы если тип не изменился
				if ($type <> $columns[$code]['udt_name']) {
					$data[] = [
						'MESSAGE' => 'Change type',
						'FIELD'   => $code,
						'REASON'  => "{$columns[$code]['udt_name']} -> $type",
						'SQL'     => "ALTER COLUMN {$code_quoted} TYPE {$type}",
					];
				}

				// NULL -> NOT NULL
				if ($field["NOT_NULL"] and $columns[$code]['is_nullable'] == 'YES') {
					$data[] = [
						'MESSAGE' => 'Set not null',
						'FIELD'   => $code,
						'SQL'     => "ALTER COLUMN {$code_quoted} SET NOT NULL",
					];
				}
				// NOT NULL -> NULL
				if (!$field["NOT_NULL"] and $columns[$code]['is_nullable'] == 'NO') {
					$data[] = [
						'MESSAGE' => 'Drop not null',
						'FIELD'   => $code,
						'SQL'     => "ALTER COLUMN {$code_quoted} DROP NOT NULL",
					];
				}

				// $default
				if (!$field["AUTO_INCREMENT"]) {

					if (!empty($field['DEFAULT'])) {
						$default = !is_array($field['DEFAULT']) ? $field['DEFAULT'] : implode(',', $field['DEFAULT']);
						if ($default <> $columns[$code]['column_default']) {
							$data[] = [
								'MESSAGE' => 'Set default',
								'FIELD'   => $code,
								'SQL'     => "ALTER COLUMN {$code_quoted} SET DEFAULT '{$default}'",
							];
						}
					} else {
						if (!empty($columns[$code]['column_default'])) {
							$data[] = [
								'MESSAGE' => 'Drop default',
								'FIELD'   => $code,
								'SQL'     => "ALTER COLUMN {$code_quoted} DROP DEFAULT",
							];
						}
					}

				} else {
					/* TODO
					$seq_name = "{$Table->code}_{$code}_seq";
					$this->Query("CREATE SEQUENCE IF NOT EXISTS {$seq_name}");
					$this->Query("SELECT setval('{$seq_name}', COALESCE((SELECT MAX({$code_id})+1 FROM {$Table->code}), 1), false)");
					$rows[] = "ALTER COLUMN {$code_id} SET DEFAULT nextval('{$seq_name}')";
					*/
				}

				unset($columns[$code]);
			}

			// DROP COLUMN:
			foreach ($columns as $code => $column) {
				$data[] = [
					'MESSAGE' => 'Drop default',
					'FIELD'   => $code,
					'SQL'     => "DROP COLUMN " . $this->Quote($code),
				];
				unset($columns[$code]);
			}

			/* TODO
			if (!empty($keys)) {
				$rows[] = "DROP CONSTRAINT IF EXISTS \"{$Table->code}_pkey\", ADD CONSTRAINT \"{$Table->code}_pkey\" PRIMARY KEY (\"" . implode("\", \"", $keys) . "\")";
			}

			if (!empty($renames)) {
				$SQL = "ALTER TABLE {$Table->code}\r\n" . implode(",\r\n", $renames) . ";";
				$this->Query($SQL);
			}

			if (!empty($rows)) {
				$SQL = "ALTER TABLE {$Table->code}\r\n" . implode(",\r\n", $rows) . ";";
				$this->Query($SQL);
			}
			*/

			if (!empty($data)) {
				$SQL = "ALTER TABLE {$Table->code} \r\n" . implode(",\r\n", array_column($data, 'SQL'));
				$diff[] = [
					'MESSAGE' => 'Modify table',
					'TABLE'   => $Table->code,
					'DATA'    => $data,
					'SQL'     => $SQL,
				];
			}
		}

		return $diff;

		// Indexes:
		$SQL = "SELECT
			    a.attname as column_name,
			    i.relname as index_name,
			    ix.indisunique as index_unique
			FROM
			    pg_class t,
			    pg_class i,
			    pg_index ix,
			    pg_attribute a
			WHERE
			    t.oid = ix.indrelid
			    and i.oid = ix.indexrelid
			    and a.attrelid = t.oid
			    and a.attnum = ANY(ix.indkey)
			    and t.relkind = 'r'
			    and t.relname ='{$Table->code}'
			ORDER BY
			    t.relname,
			    i.relname
			    ";
		$indexes = $this->Query($SQL, 'column_name');
		//debug($indexes, '$indexes');

		foreach ($Table->fields as $code => $field) {
			if (in_array($code, $keys)) {
				continue;
			}
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
			$index = $indexes[$code];

			// index is: present in database, missing in code - drop it
			if (isset($index) and !$field['INDEX']) {
				$this->Query("DROP INDEX " . $this->Quote($index['index_name']));
				continue;
			}

			// index is: missing in database, present in code - create it
			if ($field['INDEX'] and !isset($index)) {
				$this->Query("CREATE {$unique} INDEX ON {$Table->code} (" . $this->Quote($code) . ")");
				continue;
			}

			// index is: present in database, present in code - check unique
			if (isset($index)) {
				if (($field['UNIQUE'] and $index['index_unique']) or (!$field['UNIQUE'] and !$index['index_unique'])) {
					$this->Query("DROP INDEX " . $this->Quote($index['index_name']));
					$this->Query("CREATE {$unique} INDEX ON {$Table->code} (" . $this->Quote($code) . ")");
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
			$this->Query("ALTER TABLE \"{$table}\" ADD FOREIGN KEY (\"{$code}\") REFERENCES \"{$Link->code}\" (\"{$link_key}\") ON DELETE {$action} ON UPDATE {$action}");
		}
	}

	public function CompileSQLSelect(array $parts) {
		$SQL = [];
		$SQL[] = 'SELECT';
		// if ($parts['LIMIT']) $SQL[] = 'SQL_CALC_FOUND_ROWS';
		$SQL[] = implode(",\r\n", $parts['SELECT']);
		$SQL[] = "FROM {$parts['TABLE']}";
		$SQL[] = implode("\r\n", $parts['JOIN']);
		if ($parts['WHERE']) $SQL[] = "WHERE " . implode("\r\nAND ", $parts['WHERE']);
		if ($parts['GROUP']) $SQL[] = "GROUP BY " . implode(", ", $parts['GROUP']);
		if ($parts['ORDER']) $SQL[] = "ORDER BY " . implode(", ", $parts['ORDER']);
		if ($parts['LIMIT']) $SQL[] = "LIMIT {$parts['LIMIT']['COUNT']} OFFSET {$parts['LIMIT']['FROM']}";
		return implode("\r\n", $SQL);
	}

	private function RecreateEnumType() {
		// TODO make Postgres enum types from strings to actually enums
		/*
		 CREATE TYPE admin_level1 AS ENUM ('classifier', 'moderator');

		CREATE TABLE blah (
		    user_id integer primary key,
		    power admin_level1 not null
		);

		INSERT INTO blah(user_id, power) VALUES (1, 'moderator'), (10, 'classifier');

		ALTER TYPE admin_level1 ADD VALUE 'god';

		INSERT INTO blah(user_id, power) VALUES (42, 'god');

		-- .... oops, maybe that was a bad idea

		CREATE TYPE admin_level1_new AS ENUM ('classifier', 'moderator');

		-- Remove values that won't be compatible with new definition
		-- You don't have to delete, you might update instead
		DELETE FROM blah WHERE power = 'god';

		-- Convert to new type, casting via text representation
		ALTER TABLE blah
		  ALTER COLUMN power TYPE admin_level1_new
		    USING (power::text::admin_level1_new);

		-- and swap the types
		DROP TYPE admin_level1;

		ALTER TYPE admin_level1_new RENAME TO admin_level1;
		 */
	}

	public function Truncate($table) {
		$this->Query("TRUNCATE TABLE {$table} RESTART IDENTITY");
	}
}