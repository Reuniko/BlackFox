<?php

namespace System;
/**
 * Class Type
 * @package System
 *
 * Parent for all data types for using in database.
 */
abstract class Type extends Instanceable {
	public $name;
	public $code;

	abstract function GetStructureStringType($info = []);

	public function ProvideInfoIntegrity($info = []) {
		return $info;
	}

	public function GetStructureString($code, $info = []) {

		$info = $this->ProvideInfoIntegrity($info);

		$type = $this->GetStructureStringType($info);

		$null = ($info["NOT_NULL"]) ? "NOT NULL" : "NULL";

		$default = "";
		if ($info['DEFAULT']) {
			if (is_array($info['DEFAULT'])) {
				$info['DEFAULT'] = implode(',', $info['DEFAULT']);
			}
			$default = "DEFAULT '{$info['DEFAULT']}'";
		}

		$auto_increment = ($info["AUTO_INCREMENT"]) ? "AUTO_INCREMENT" : "";

		$comment = ($info["NAME"]) ? " COMMENT '{$info["NAME"]}'" : "";

		$structure_string = "`{$code}` $type $null $default $auto_increment $comment";

		return $structure_string;
	}

	/**
	 * Format input value from user to save into database.
	 * No escape required.
	 *
	 * @param mixed $value input value from user
	 * @param array $info type info
	 * @return string input value for database
	 */
	public function FormatInputValue($value, $info = []) {
		return $value;
	}

	/**
	 * Format output value from database to user.
	 * No escape required.
	 *
	 * @param string $value output value from database
	 * @param array $info type info
	 * @return mixed output value for user
	 */
	public function FormatOutputValue($value, $info = []) {
		return $value;
	}
}