<?php

namespace System;
/**
 * Class AType
 * @package System
 *
 * Родитель для всех типов данных, используемых в качестве полей базы данных
 */
abstract class AType {
	public $name;

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

	public function FormatValue($value, $info = []) {
		return $value;
	}
}