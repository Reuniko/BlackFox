<?php

namespace System;

class CacheData extends SCRUD {
	public $name = 'Данные кеша';
	public $structure = [
		'KEY'     => [
			'TYPE'     => 'STRING',
			'NAME'     => 'KEY',
			'INDEX'    => true,
			'PRIMARY'  => true,
			'NOT_NULL' => true,
			'DISABLED' => true,
			'VITAL'    => true,
		],
		'TYPE'    => [
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
		'VALUE'   => [
			'TYPE' => 'TEXT',
			'NAME' => 'VALUE',
		],
		'CREATED' => [
			'TYPE'     => 'DATETIME',
			'NAME'     => 'CREATED',
			'NOT_NULL' => true,
		],
		'EXPIRE'  => [
			'TYPE' => 'DATETIME',
			'NAME' => 'EXPIRE',
		],
		'TAGS'    => [
			'TYPE'  => 'INNER',
			'NAME'  => 'TAGS',
			'LINK'  => 'CacheTags',
			'FIELD' => 'KEY',
		],
	];

	public function Create($fields) {
		$fields['CREATED'] = time();
		return parent::Create($fields);
	}

}