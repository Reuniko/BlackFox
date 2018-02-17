<?php
namespace System;

class TestScrudBase extends Test {

	public $name = 'Тест SCRUD: базовые методы';

	/** @var SCRUD $SCRUD */
	public $SCRUD = null;
	public $limit = 100;

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
		for ($i = 0; $i < $this->limit; $i++) {
			$R[] = $this->SCRUD->Create([
				'BOOL'     => array_rand([true, false]),
				'NUMBER'   => random_int(0, 99),
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
			$this->SCRUD->Create(['ENUM' => 'BAD_VALUE']);
		} catch (Exception $error) {
			return $error->getMessage();
		}
		throw new Exception("Ожидалась ошибка при попытке вставить в поле типа ENUM неизвестное значение");
	}

	/** Попытка некорректного обновления поля типа ENUM */
	public function TestUpdateBadRow() {
		try {
			$this->SCRUD->Update(1, ['ENUM' => 'BAD_VALUE']);
		} catch (Exception $error) {
			return $error->getMessage();
		}
		throw new Exception("Ожидалась ошибка при попытке обновить в поле типа ENUM неизвестное значение");
	}

	/** Проверка фильтра: булевое значение */
	public function TestFilterByBool() {
		$value = (bool)rand(0, 1);
		$elements = $this->SCRUD->GetList([
			'FILTER' => ['BOOL' => $value],
			'FIELDS' => ['ID', 'BOOL'],
		]);
		foreach ($elements as $id => $element) {
			if ($element['BOOL'] <> $value) {
				throw new Exception("Элемент #{$id} значение BOOL <> {$value}");
			}
		}
		return count($elements);
	}

	/** Проверка фильтра: целочисленное значение */
	public function TestFilterByNumber() {
		$value = rand(0, 99);
		$elements = $this->SCRUD->GetList([
			'FILTER' => ['NUMBER' => $value],
			'FIELDS' => ['ID', 'NUMBER'],
		]);
		foreach ($elements as $id => $element) {
			if ($element['NUMBER'] <> $value) {
				throw new Exception("Элемент #{$id} значение NUMBER <> {$value}");
			}
		}
		return count($elements);
	}

	/** Проверка фильтра: строковое значение целиком */
	public function TestFilterByString() {
		$value = $this->SCRUD->Read(rand(1, $this->limit), ['ID', 'STRING'])['STRING'];
		$elements = $this->SCRUD->GetList([
			'FILTER' => ['STRING' => $value],
			'FIELDS' => ['ID', 'STRING'],
		]);
		foreach ($elements as $id => $element) {
			if ($element['STRING'] <> $value) {
				throw new Exception("Элемент #{$id} значение STRING <> {$value}");
			}
		}
		return $value;
	}

	/** Проверка фильтра: строковое значение подстрокой */
	public function TestFilterBySubString() {
		$value = $this->SCRUD->Read(rand(1, $this->limit), ['ID', 'STRING'])['STRING'];
		$value = substr($value, rand(1, 3), rand(3, 5));
		$elements = $this->SCRUD->GetList([
			'FILTER' => ['%STRING' => $value],
			'FIELDS' => ['ID', 'STRING'],
		]);
		foreach ($elements as $id => $element) {
			if (strpos($element['STRING'], $value) === false) {
				throw new Exception("Элемент #{$id} значение STRING !~ {$value}");
			}
		}
		return $value;
	}
}

