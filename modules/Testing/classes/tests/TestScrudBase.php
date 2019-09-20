<?php

namespace Testing;

class TestScrudBase extends Test {
	public $name = 'Test SCRUD: base methods';

	/** @var \System\SCRUD $SCRUD */
	public $SCRUD = null;
	public $limit = 250;

	/** Getting instance of TestScrudBase */
	public function TestGetInstance() {
		$this->SCRUD = Table1::I();
	}

	/** Structure synchronization */
	public function TestSynchronize() {
		$this->SCRUD->Synchronize();
	}

	/** Deleting of all records */
	public function TestTruncate() {
		$this->SCRUD->Truncate();
		$rows = $this->SCRUD->Select();
		if (!empty($rows)) {
			throw new Exception(T([
				'en' => 'There are records in the table',
				'ru' => 'В таблице остались записи',
			]));
		}
	}

	/** Creating random records
	 */
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

	/** Attempt to create an incorrect entry with a field of type ENUM */
	public function TestCreateBadRow() {
		try {
			$this->SCRUD->Create(['ENUM' => 'BAD_VALUE']);
		} catch (\Exception $error) {
			return $error->getMessage();
		}
		throw new Exception(T([
			'en' => 'An error was expected when trying to insert an unknown value in the ENUM type field',
			'ru' => 'Ожидалась ошибка при попытке вставить в поле типа ENUM неизвестное значение',
		]));
	}

	/** Attempt to incorrectly update a field of type ENUM */
	public function TestUpdateBadRow() {
		try {
			$this->SCRUD->Update(1, ['ENUM' => 'BAD_VALUE']);
		} catch (\Exception $error) {
			return $error->getMessage();
		}
		throw new Exception(T([
			'en' => 'An error was expected while trying to update an unknown value in the ENUM type field',
			'ru' => 'Ожидалась ошибка при попытке обновить в поле типа ENUM неизвестное значение',
		]));
	}

	/** Filter check: boolean value */
	public function TestFilterByBool() {
		foreach ([true, false] as $value) {
			$elements = $this->SCRUD->Select([
				'FILTER' => ['BOOL' => $value],
				'FIELDS' => ['ID', 'BOOL'],
			]);
			foreach ($elements as $id => $element) {
				if ($element['BOOL'] <> $value) {
					throw new Exception("Element #{$id}: value BOOL: {$value} <> {$element['BOOL']}");
				}
			}
		}
		return count($elements);
	}

	/** Filter check: integer value */
	public function TestFilterByNumber() {
		$value = rand(0, 99);
		$elements = $this->SCRUD->Select([
			'FILTER' => ['NUMBER' => $value],
			'FIELDS' => ['ID', 'NUMBER'],
		]);
		foreach ($elements as $id => $element) {
			if ($element['NUMBER'] <> $value) {
				throw new Exception("Element #{$id}: value NUMBER <> {$value}");
			}
		}
		return count($elements);
	}

	/** Filter check: string value */
	public function TestFilterByString() {
		$value = $this->SCRUD->Read(rand(1, $this->limit), ['ID', 'STRING'])['STRING'];
		$elements = $this->SCRUD->Select([
			'FILTER' => ['STRING' => $value],
			'FIELDS' => ['ID', 'STRING'],
		]);
		foreach ($elements as $id => $element) {
			if ($element['STRING'] <> $value) {
				throw new Exception("Element #{$id}: value STRING <> {$value}");
			}
		}
		return $value;
	}

	/** Filter check: substring value */
	public function TestFilterBySubString() {
		$value = $this->SCRUD->Read([], ['ID', 'STRING'], ['{RANDOM}' => '']);
		$value = substr($value['STRING'], rand(1, 3), rand(3, 5));
		if (empty($value)) {
			throw new Exception(T([
				'en' => 'Could not retrieve random substring',
				'ru' => 'Не удалось извлечь случайную подстроку',
			]));
		}
		$elements = $this->SCRUD->Select([
			'FILTER' => ['~STRING' => $value],
			'FIELDS' => ['ID', 'STRING'],
		]);
		foreach ($elements as $id => $element) {
			if (strpos($element['STRING'], $value) === false) {
				throw new Exception("Element #{$id}: value STRING: '{$value}' <> '{$element['STRING']}'");
			}
		}
		return $value;
	}

	/** Filter check: approximate date */
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

	/** Pager check: no filtering */
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

	/** Count check */
	public function TestCount() {
		$count[1] = $this->SCRUD->Count([]);
		$count[2] = $this->SCRUD->Count(['BOOL' => true]);
		$count[3] = $this->SCRUD->Count(['BOOL' => false]);
		if ($count[1] <> $count[2] + $count[3]) {
			throw new Exception("Unexpected checksum");
		}
		return "{$count[1]} = {$count[2]} + {$count[3]}";
	}
}