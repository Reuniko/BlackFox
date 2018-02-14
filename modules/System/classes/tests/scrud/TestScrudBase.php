<?php
namespace System;

class TestScrudBase extends Test {

	public $name = 'Базовые тесты SCRUD';

	/** @var SCRUD $SCRUD */
	public $SCRUD = null;

	/** Взятие инстанса класса "TestScrudBase" */
	public function TestGetInstance() {
		$this->SCRUD = TestScrudTableSimple::I();
	}

	/** Синхронизация структуры */
	public function TestSynchronize() {
		$this->SCRUD->Synchronize();
	}

	/** Удаление всех записей */
	public function TestTruncate() {
		$this->SCRUD->Truncate();
		$rows = $this->SCRUD->GetList();
		if (!empty($rows)) {
			throw new Exception("В таблице остались записи");
		}
	}

	/** Создание 100 случайных записей */
	public function TestCreateRandomRows() {
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
		return $i;
	}

	/** Попытка создания некорректной записи с полем типа ENUM */
	public function TestCreateBadRow() {
		try {
			$this->SCRUD->Create([
				'ENUM' => 'BAD_VALUE',
			]);
		} catch (Exception $error) {
			return $error->getMessage();
		}
		throw new Exception("Ожидалась ошибка при попытке вставить в поле типа ENUM неизвестное значение");
	}

	/** Попытка некорректного обновления поля типа ENUM */
	public function TestUpdateBadRow() {
		try {
			$this->SCRUD->Update(1, [
				'ENUM' => 'BAD_VALUE',
			]);
		} catch (Exception $error) {
			return $error->getMessage();
		}
		throw new Exception("Ожидалась ошибка при попытке обновить в поле типа ENUM неизвестное значение");
	}
}

