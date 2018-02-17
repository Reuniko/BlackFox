<?php

namespace System;

class TestScrudExplainFields extends Test {
	public $name = 'Тест SCRUD: метод ExplainFields';

	/** @var SCRUD $SCRUD */
	public $SCRUD = null;
	public $limit = 100;

	/** Взятие инстанса класса "TestScrudBase" */
	public function TestGetInstance() {
		$this->SCRUD = TestScrudTableSimple::I();
	}

	/** * - все поля 1 уровня */
	public function Test1A() {
		$fields = $this->SCRUD->ExplainFields(['*']);
		//debug(var_export($fields, true));
		$awaits = array(
			'ID'       => 'ID',
			'BOOL'     => 'BOOL',
			'NUMBER'   => 'NUMBER',
			'FLOAT'    => 'FLOAT',
			'STRING'   => 'STRING',
			'LINK'     => 'LINK',
			'TEXT'     => 'TEXT',
			'DATETIME' => 'DATETIME',
			'TIME'     => 'TIME',
			'DATE'     => 'DATE',
			'ENUM'     => 'ENUM',
			'SET'      => 'SET',
			'FILE'     => 'FILE',
		);
		if ($fields <> $awaits) {
			throw new Exception($fields);
		}
	}

	/** @ - важные поля 1 уровня */
	public function Test1V() {
		$fields = $this->SCRUD->ExplainFields(['@']);
		//debug(var_export($fields, true));
		$awaits = array(
			'ID'     => 'ID',
			'STRING' => 'STRING',
		);
		if ($fields <> $awaits) {
			throw new Exception($fields);
		}
	}

	/** ** - все поля 1 уровня, все поля 2 уровня */
	public function Test1A2A() {
		$fields = $this->SCRUD->ExplainFields(['**']);
		//debug(var_export($fields, true));
		$awaits = array(
			'ID'       => 'ID',
			'BOOL'     => 'BOOL',
			'NUMBER'   => 'NUMBER',
			'FLOAT'    => 'FLOAT',
			'STRING'   => 'STRING',
			'LINK'     => array(
				'ID'       => 'ID',
				'BOOL'     => 'BOOL',
				'NUMBER'   => 'NUMBER',
				'FLOAT'    => 'FLOAT',
				'STRING'   => 'STRING',
				'LINK'     => 'LINK',
				'TEXT'     => 'TEXT',
				'DATETIME' => 'DATETIME',
				'TIME'     => 'TIME',
				'DATE'     => 'DATE',
				'ENUM'     => 'ENUM',
				'SET'      => 'SET',
				'FILE'     => 'FILE',
			),
			'TEXT'     => 'TEXT',
			'DATETIME' => 'DATETIME',
			'TIME'     => 'TIME',
			'DATE'     => 'DATE',
			'ENUM'     => 'ENUM',
			'SET'      => 'SET',
			'FILE'     => 'FILE',
		);
		if ($fields <> $awaits) {
			throw new Exception($fields);
		}
	}

	/** *@ - все поля 1 уровня, важные поля 2 уровня */
	public function Test1A2V() {
		$fields = $this->SCRUD->ExplainFields(['*@']);
		//debug(var_export($fields, true));
		$awaits = array(
			'ID'       => 'ID',
			'BOOL'     => 'BOOL',
			'NUMBER'   => 'NUMBER',
			'FLOAT'    => 'FLOAT',
			'STRING'   => 'STRING',
			'LINK'     => array(
				'ID'     => 'ID',
				'STRING' => 'STRING',
			),
			'TEXT'     => 'TEXT',
			'DATETIME' => 'DATETIME',
			'TIME'     => 'TIME',
			'DATE'     => 'DATE',
			'ENUM'     => 'ENUM',
			'SET'      => 'SET',
			'FILE'     => 'FILE',
		);
		if ($fields <> $awaits) {
			throw new Exception($fields);
		}
	}

	/** составная выборка */
	public function TestComplex() {
		$fields = $this->SCRUD->ExplainFields([
			'@',
			'BOOL',
			'LINK' => ['@', 'NUMBER', 'FILE'],
		]);
		//debug(var_export($fields, true));
		$awaits = array(
			'ID'     => 'ID',
			'STRING' => 'STRING',
			'BOOL'   => 'BOOL',
			'LINK'   => array(
				'ID'     => 'ID',
				'STRING' => 'STRING',
				'NUMBER' => 'NUMBER',
				'FILE'   => 'FILE',
			),
		);
		if ($fields <> $awaits) {
			throw new Exception($fields);
		}
	}
}