<?php
namespace System;
class TestScrudBase extends Test {
	public $name = 'Базовые тесты SCRUD';
	public $tests = [
		'GetInstance'      => 'Взятие инстанса класса',
		'Synchronize'      => 'Синхронизация структуры',
		'Truncate'         => 'Удаление всех записей',
		'CreateRandomRows' => 'Создание случайных записей',
	];
	/** @var SCRUD $SCRUD */
	public $SCRUD = null;

	public function GetInstance() {
		$this->SCRUD = TestScrudTableSimple::I();
	}

	public function Synchronize() {
		$this->SCRUD->Synchronize();
	}

	public function Truncate() {
		$this->SCRUD->Truncate();
		$rows = $this->SCRUD->GetList();
		if (!empty($rows)) {
			throw new Exception("В таблице остались записи");
		}
	}

	public function CreateRandomRows() {
		$R = [];
		for ($i = 0; $i < 100; $i++) {
			$R[] = $this->SCRUD->Create([
				'BOOL'     => array_rand([true, false]),
				'NUMBER'   => random_int(0, 99999),
				'FLOAT'    => random_int(0, 99999) / random_int(1, 9),
				'STRING'   => sha1(random_bytes(8)),
				'LINK'     => array_rand($R),
				'TEXT'     => str_repeat(sha1(random_bytes(8)) . ' ', 50),
				'DATETIME' => time() + random_int(-99999, 99999),
				'TIME'     => random_int(0, 23) . ':' . random_int(0, 59),
				'DATE'     => '+' . random_int(2, 90) . ' days',
				'ENUM'     => array_rand($this->SCRUD->structure['ENUM']['VALUES']),
				'SET'      => array_rand($this->SCRUD->structure['SET']['VALUES']),
				'FILE'     => null,
			]);
		}
		return $R;
	}
}

