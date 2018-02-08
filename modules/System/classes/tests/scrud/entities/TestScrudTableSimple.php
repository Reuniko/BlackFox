<?php
namespace System;

class TestScrudTableSimple extends SCRUD {

	public function Init() {
		$this->name = 'Простая таблица для тестирования';
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
				'TYPE' => 'STRING',
				'NAME' => 'String',
				'JOIN' => true,
				'SHOW' => true,
			],
			'LINK'     => [
				'TYPE' => 'LINK',
				'NAME' => 'Link to self',
				'LINK' => 'System\TestScrudTableSimple',
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
					'VALUE_1' => 'VALUE 1',
					'VALUE_2' => 'VALUE 2',
					'VALUE_3' => 'VALUE 3',
				],
			],
			'SET'      => [
				'TYPE'   => 'SET',
				'NAME'   => 'Set',
				'VALUES' => [
					'VALUE_4' => 'VALUE 4',
					'VALUE_5' => 'VALUE 5',
					'VALUE_6' => 'VALUE 6',
					'VALUE_7' => 'VALUE 7',
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