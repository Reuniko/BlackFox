<?php

namespace System;

class CacheTags extends SCRUD {
	public $name = 'Cache tags';
	public $structure = [
		'KEY' => [
			'TYPE'    => 'STRING',
			'NAME'    => 'KEY',
			'PRIMARY' => true,
		],
		'TAG' => [
			'TYPE'    => 'STRING',
			'NAME'    => 'TAG',
			'PRIMARY' => true,
		],
	];
}