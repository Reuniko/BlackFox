<?php

namespace BlackFox;

class FactoryType {

	use Instance;

	public static $TYPES = [
		'ARRAY'    => '\BlackFox\TypeArray',
		'BOOLEAN'  => '\BlackFox\TypeBoolean',
		'DATE'     => '\BlackFox\TypeDate',
		'DATETIME' => '\BlackFox\TypeDateTime',
		'ENUM'     => '\BlackFox\TypeEnum',
		'FLOAT'    => '\BlackFox\TypeFloat',
		'INNER'    => '\BlackFox\TypeInner',
		'LIST'     => '\BlackFox\TypeList',
		'INTEGER'  => '\BlackFox\TypeInteger',
		'OUTER'    => '\BlackFox\TypeOuter',
		'PASSWORD' => '\BlackFox\TypePassword',
		'SET'      => '\BlackFox\TypeSet',
		'STRING'   => '\BlackFox\TypeString',
		'TEXT'     => '\BlackFox\TypeText',
		'TIME'     => '\BlackFox\TypeTime',
		'FILE'     => '\BlackFox\TypeFile',
	];

	public static function Add($name, $class) {
		self::$TYPES[$name] = $class;
	}

	/**
	 * Get instance of class mapped to code of the type
	 *
	 * @param array $info info of the field
	 * @param Database $Database
	 * @return Type instance of class
	 * @throws Exception
	 */
	public static function Get(array $info, Database $Database) {
		$info['TYPE'] = strtoupper($info['TYPE']);
		if (!isset(self::$TYPES[$info['TYPE']])) {
			throw new Exception("Class for type '{$info['TYPE']}' not found, field code: '{$info['CODE']}'");
		}
		/** @var Type $class */
		$class = self::$TYPES[$info['TYPE']];
		return new $class($info, $Database);
	}

}