<?php

namespace Testing;

class Table1 extends \System\SCRUD {

	public function Init() {
		$this->name = 'Table1';
		$this->structure = [
			'ID'       => self::ID,
			'BOOL'     => [
				'TYPE' => 'BOOL',
				'NAME' => 'Bool',
			],
			'NUMBER'   => [
				'TYPE' => 'NUMBER',
				'NAME' => 'Number',
			],
			'FLOAT'    => [
				'TYPE' => 'FLOAT',
				'NAME' => 'Float',
			],
			'STRING'   => [
				'TYPE'  => 'STRING',
				'NAME'  => 'String',
				'VITAL' => true,
			],
			'LINK'     => [
				'TYPE' => 'OUTER',
				'NAME' => 'Link to self',
				'LINK' => 'Testing\Table1',
			],
			'TEXT'     => [
				'TYPE' => 'TEXT',
				'NAME' => 'Text',
			],
			'DATETIME' => [
				'TYPE' => 'DATETIME',
				'NAME' => 'Datetime',
			],
			'TIME'     => [
				'TYPE' => 'TIME',
				'NAME' => 'Time',
			],
			'DATE'     => [
				'TYPE' => 'DATE',
				'NAME' => 'Date',
			],
			'ENUM'     => [
				'TYPE'   => 'ENUM',
				'NAME'   => 'Enum',
				'VALUES' => [
					'VALUE_1' => 'VALUE_1',
					'VALUE_2' => 'VALUE_2',
					'VALUE_3' => 'VALUE_3',
				],
			],
			'SET'      => [
				'TYPE'   => 'SET',
				'NAME'   => 'Set',
				'VALUES' => [
					'VALUE_4' => 'VALUE_4',
					'VALUE_5' => 'VALUE_5',
					'VALUE_6' => 'VALUE_6',
					'VALUE_7' => 'VALUE_7',
				],
			],
			'FILE'     => [
				'TYPE' => 'FILE',
				'NAME' => 'File',
				'LINK' => 'System\Files',
			],
		];
	}

}