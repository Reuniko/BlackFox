<?php

namespace System;

class CacheData extends SCRUD {
	public $name = 'Данные кеша';
	public $structure = [
		'KEY'    => [
			'TYPE'     => 'STRING',
			'NAME'     => 'KEY',
			'INDEX'    => true,
			'PRIMARY'  => true,
			'NOT_NULL' => true,
			'DISABLED' => true,
			'VITAL'    => true,
		],
		'TYPE'   => [
			'NAME'     => 'TYPE',
			'TYPE'     => 'ENUM',
			'NOT_NULL' => true,
			'VALUES'   => [
				'boolean' => 'boolean',
				'integer' => 'integer',
				'double'  => 'double',
				'string'  => 'string',
				'array'   => 'array',
				'object'  => 'object',
			],
		],
		'VALUE'  => [
			'TYPE' => 'TEXT',
			'NAME' => 'VALUE',
		],
		'EXPIRE' => [
			'TYPE' => 'DATETIME',
			'NAME' => 'EXPIRE',
		],
		'TAGS'   => [
			'TYPE'  => 'INNER',
			'NAME'  => 'TAGS',
			'LINK'  => 'CacheTags',
			'FIELD' => 'KEY',
		],
	];
}