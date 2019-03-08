<?php

namespace System;

class DatabaseDriverPostgres extends Database {

	private $link;

	public function __construct($params = []) {

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
		$result = pg_query($this->link, $SQL);
		if ($result === false) {
			throw new ExceptionSQL(pg_last_error($this->link) . $SQL, $SQL);
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

	public function SynchronizeTable($table, $structure) {
		$strict = true;
		if (empty($table)) {
			throw new Exception("Synchronize failed: no code of table");
		}
		if (empty($structure)) {
			throw new Exception("Synchronize of '{$table}' failed: structure is empty");
		}
		$structure = array_change_key_case($structure, CASE_UPPER);

		$check = $this->Query("SELECT * FROM pg_catalog.pg_tables WHERE tablename='{$table}'");

		if (empty($check)) {

			$rows = [];
			$keys = [];
			foreach ($structure as $code => $Type) {
				/** @var Type $Type */
				if ($Type['PRIMARY']) {
					$keys[] = $code;
				}
				try {
					$db_type = $Type->GetStructureStringType();
				} catch (\Exception $error) {
					continue;
				}
				$null = ($Type["NOT_NULL"] || $Type['PRIMARY']) ? "NOT NULL" : "NULL";
				$default = "";
				if ($Type['DEFAULT']) {
					$default = !is_array($Type['DEFAULT']) ? $Type['DEFAULT'] : implode(',', $Type['DEFAULT']);
					$default = "DEFAULT '{$default}'";
				}
				if ($Type["AUTO_INCREMENT"]) {
					$db_type = 'serial';
				}
				$rows[] = $this->Quote($Type['CODE']) . " $db_type $null $default";
			}
			if (!empty($keys)) {
				$rows[] = "PRIMARY KEY (" . implode(", ", array_map([$this, 'Quote'], $keys)) . ")";
			}
			$rows = "CREATE TABLE IF NOT EXISTS {$table} \r\n" . "(" . implode(",\r\n", $rows) . ");";
			$this->Query($rows);

		} else {

			$columns = $this->Query("SELECT * FROM information_schema.columns WHERE table_name ='{$table}';", 'column_name');
			$columns = array_change_key_case($columns, CASE_UPPER);

			$rows = [];
			$keys = [];

			foreach ($structure as $code => $Type) {
				/** @var Type $Type */
				if ($Type['PRIMARY']) {
					$keys[] = $code;
				}
				// type
				try {
					$db_type = $Type->GetStructureStringType();
				} catch (\Exception $error) {
					continue;
				}

				// TODO добавить возможность RENAME COLUMN
				if (!isset($columns[$code])) {
					if ($Type["AUTO_INCREMENT"]) {
						$db_type = 'serial';
					}
					$rows[] = "ADD COLUMN \"{$code}\" {$db_type}";
				} else {
					$rows[] = "ALTER COLUMN \"{$code}\" TYPE {$db_type}";
				}

				// null
				if (($Type["NOT_NULL"] || $Type['PRIMARY'])) {
					$rows[] = "ALTER COLUMN \"{$code}\" SET NOT NULL";
				} else {
					$rows[] = "ALTER COLUMN \"{$code}\" DROP NOT NULL";
				}

				// default
				if ($Type["AUTO_INCREMENT"]) {
					// TODO generate sequences manually
					$default = 'nextval(\'"' . $table . '_' . $code . '_seq"\'::regclass)';
					$rows[] = "ALTER COLUMN \"{$code}\" SET DEFAULT {$default}";
				} else {
					if (isset($Type['DEFAULT'])) {
						$default = !is_array($Type['DEFAULT']) ? $Type['DEFAULT'] : implode(',', $Type['DEFAULT']);
						$rows[] = "ALTER COLUMN \"{$code}\" SET DEFAULT '{$default}'";
					} else {
						$rows[] = "ALTER COLUMN \"{$code}\" DROP DEFAULT";
					}
				}

				unset($columns[$code]);
			}
			if ($strict) {
				foreach ($columns as $code => $column) {
					$rows[] = "DROP COLUMN \"{$code}\"";
				}
			}
			if (!empty($keys)) {
				$rows[] = "DROP CONSTRAINT IF EXISTS \"{$table}_pkey\", ADD CONSTRAINT \"{$table}_pkey\" PRIMARY KEY (\"" . implode("\", \"", $keys) . "\")";
			}
			$SQL = "ALTER TABLE {$table}\r\n" . implode(",\r\n", $rows) . ";";
			$this->Query($SQL);
		}

		// $indexes = $this->Query("SELECT * FROM pg_indexes WHERE tablename ='{$code}'", 'Column_name');
		// TODO: проверять, добавлять и удалять индексы

		/*
			select
			    t.relname as table_name,
			    i.relname as index_name,
			    a.attname as column_name
			from
			    pg_class t,
			    pg_class i,
			    pg_index ix,
			    pg_attribute a
			where
			    t.oid = ix.indrelid
			    and i.oid = ix.indexrelid
			    and a.attrelid = t.oid
			    and a.attnum = ANY(ix.indkey)
			    and t.relkind = 'r'
			    and t.relname ='test1'
			order by
			    t.relname,
			    i.relname;
		 */
		$indexes = [];

		/*
		foreach ($structure as $code => $field) {
			if (in_array($code, $this->keys)) {
				continue;
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

			// в базе есть, в коде нет - удалить
			if (isset($index) && !$field['INDEX']) {
				//$this->Query("ALTER TABLE \"{$code}\" DROP INDEX \"{$code}\";");
				continue;
			}

			// в базе нет, в коде есть - добавить
			// CREATE INDEX  ON "public"."test1" ("TITLE");

			if (($field['INDEX']) && (!isset($index))) {
				//$this->Query("ALTER TABLE \"{$code}\" ADD {$unique} INDEX \"{$code}\" (\"{$code}\");");
				continue;
			}

			// в базе есть, в коде есть - уточнение уникальности индекса
			if (isset($index)) {
				if (($field['UNIQUE'] && $index['Non_unique']) || (!$field['UNIQUE'] && !$index['Non_unique'])) {
					//$this->Query("ALTER TABLE \"{$code}\" DROP INDEX \"{$code}\", ADD {$unique} INDEX \"{$code}\" (\"{$code}\");");
					continue;
				}
			}
		}
		*/
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
}