<?php

namespace Testing;

class TestScrudBase extends Test {
	public $name = 'Тест SCRUD: базовые методы';

	/** @var \System\SCRUD $SCRUD */
	public $SCRUD = null;
	public $limit = 250;

	/** Взятие инстанса класса "TestScrudBase" */
	public function TestGetInstance() {
		$this->SCRUD = Table1::I();
	}

	/** Синхронизация структуры */
	public function TestSynchronize() {
		$this->SCRUD->Synchronize();
	}

	/** Удаление всех записей */
	public function TestTruncate() {
		$this->SCRUD->Truncate();
		$rows = $this->SCRUD->Select();
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
				'LINK'     => @array_rand($R),
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
		} catch (\Exception $error) {
			return $error->getMessage();
		}
		throw new Exception("Ожидалась ошибка при попытке вставить в поле типа ENUM неизвестное значение");
	}

	/** Попытка некорректного обновления поля типа ENUM */
	public function TestUpdateBadRow() {
		try {
			$this->SCRUD->Update(1, ['ENUM' => 'BAD_VALUE']);
		} catch (\Exception $error) {
			return $error->getMessage();
		}
		throw new Exception("Ожидалась ошибка при попытке обновить в поле типа ENUM неизвестное значение");
	}

	/** Проверка фильтра: булевое значение */
	public function TestFilterByBool() {
		foreach ([true, false] as $value) {
			$elements = $this->SCRUD->Select([
				'FILTER' => ['BOOL' => $value],
				'FIELDS' => ['ID', 'BOOL'],
			]);
			foreach ($elements as $id => $element) {
				if ($element['BOOL'] <> $value) {
					throw new Exception("Элемент #{$id} значение BOOL: {$value} <> {$element['BOOL']}");
				}
			}
		}
		return count($elements);
	}

	/** Проверка фильтра: целочисленное значение */
	public function TestFilterByNumber() {
		$value = rand(0, 99);
		$elements = $this->SCRUD->Select([
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
		$elements = $this->SCRUD->Select([
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
		$value = $this->SCRUD->Read([], ['ID', 'STRING'], ['{RANDOM}' => '']);
		$value = substr($value['STRING'], rand(1, 3), rand(3, 5));
		if (empty($value)) {
			throw new Exception("Не удалось извлечь случайную подстроку");
		}
		$elements = $this->SCRUD->Select([
			'FILTER' => ['~STRING' => $value],
			'FIELDS' => ['ID', 'STRING'],
		]);
		foreach ($elements as $id => $element) {
			if (strpos($element['STRING'], $value) === false) {
				throw new Exception("Элемент #{$id} значение STRING: '{$value}' <> '{$element['STRING']}'");
			}
		}
		return $value;
	}

	/** Проверка фильтра: примерная дата */
	public function TestFilterDateApproximate() {
		$values = $this->SCRUD->GetColumn(['~DATETIME' => date('d.m.Y')], 'DATETIME');
		foreach ($values as $raw) {
			$date = date('d.m.Y', strtotime($raw));
			if ($date <> date('d.m.Y')) {
				throw new Exception("Wrong date: {$date}");
			}
		}
		// return [$this->SCRUD->SQL, $values];
	}

	/** Проверка пейджера: без фильтрации */
	public function TestPager() {
		$step = 100;
		foreach ([1, 2, 3] as $page) {
			$result = $this->SCRUD->Search([
				'LIMIT'  => $step,
				'PAGE'   => $page,
				'FIELDS' => ['ID'],
			]);
			$expected_pager = [
				'TOTAL'    => $this->limit,
				'CURRENT'  => $page,
				'LIMIT'    => $step,
				'SELECTED' => min($this->limit - ($page - 1) * $step, $step),
			];
			if ($result['PAGER'] <> $expected_pager) {
				throw new Exception(["Unexpected PAGER", $expected_pager, $result['PAGER'], $this->SCRUD->SQL]);
			}
		}
	}
}