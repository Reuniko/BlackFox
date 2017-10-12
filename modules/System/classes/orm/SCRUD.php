<?php

namespace System;
/**
 * Class SCRUD -- Search, Create, Read, Update, Delete
 * @package System
 *
 * Предоставляет функционал для работы с источниками данных [с таблицами в базе данных]:
 * - синхронизация структуры таблицы со структурой, описанной в классе-наследнике (включая создание таблицы)
 * - Search - постраничный поиск [+ выборка] записей в таблице
 * - Create - создание записей
 * - Read - чтение первой подходящей по фильтрам записи
 * - Update - обновление записи
 * - Delete - удаление указанных записей
 *
 * Для того чтобы создать новый источник данных:
 * - создайте класс-наследник от SCRUD
 * - переопределите метод Init, определите в нем структуру данных $this->composition[]
 * - однократно запустите $this->Synchronize()
 * - при необходимости переопределите другие методы (например проверки целостности при создании записи)
 * - при необходимости добавьте дополнительный функционал, описывающий бизнес-логику работы с этими данными
 */
abstract class SCRUD extends Instanceable {

	/** @var array доступные типы колонок */
	public $TYPES = array(
		'STRING'   => 'varchar(255)',
		'NUMBER'   => 'int',
		'LINK'     => 'int',
		'FILE'     => 'int',
		'FLOAT'    => 'float',
		'TEXT'     => 'text',
		'BOOL'     => 'enum("N","Y")',
		'DATETIME' => 'datetime',
		'TIME'     => 'time',
		'DATE'     => 'date',
		'ENUM'     => 'enum',
		'SET'      => 'set',
	);
	/** @var string последний выполненный SQL-запрос (для отладки) */
	public $SQL;
	/** @var \System\Database коннектор базы данных */
	protected $DB;

	/** @var string имя источника данных или таблицы или сущностей в ней */
	public $name;
	/** @var string символьный код таблицы, формируется автоматически, возможно переопределить */
	public $code;

	/** @var array композиция групп полей и полей базы данных */
	public $composition = array();
	/** @var array массив полей базы данных, формируется автоматически */
	public $structure = array();
	/** @var array массив выюираемых полей базы данных, формируется автоматически */
	public $selection = array();
	/** @var array массив первичных ключей, формируется автоматически */
	public $primary_keys = array();
	/** @var array массив связанных таблиц */
	public $annexes = array();

	/**
	 * Лист ассоциативных массивов, указывающий какие формировать дополнительные
	 * вкладки связанных элементов на странице просмотра/редактирования элемента
	 * - NAME - имя вкладки (ex: "Купленные лиды")
	 * - LINK - имя класса-наследника SCRUD (ex: CWLClientPrivate)
	 * - TARGET - поле исходного объекта, по которому ищутся связанные элементы (ex: "LEAD")
	 *
	 * @var array
	 */
	public $externals = array();

	/**
	 * Лист ассоциативных массивов, указывающий какие формировать действия на
	 * административной странице просмотра/редактирования элемента
	 * - NAME - имя действия (ex: "Копировать тариф")
	 * - DESCRIPTION - описание действия (ex: "Копирует тариф вместе с тарифной сеткой")
	 * - PARAMS - параметры вызывающегося метода в виде ассоциативного массива, ключи которого
	 * представляют собой название переменных, значения описывают структуру полей для визуализации в
	 * административной части
	 * - METHOD - название метода в классе, вызываемого при активации действия, обязательно должен
	 * принимать на вход параметр $ID
	 * - ANSWER - функция, обрабатывающая результат работы действия, принимает на вход один
	 * параметр - результат работы действия, должна вернуть строку, описывающую результат работы
	 * - NO_WARNING - не показывать предупреждение при совершении действия ?
	 * - NEW_WINDOW - открывать результат в новом окне ?
	 *
	 * @var array
	 */
	public $actions = array();

	/**
	 * Лист ассоциативных массивов, указывающий какие формировать действия на
	 * административной странице просмотра списка элементов
	 * - NAME - имя действия (ex: "Деактивировать")
	 * - DESCRIPTION - описание действия (ex: "Деактивирует выбранные элементы")
	 * - METHOD - название метода в классе, вызываемого при активации действия, обязательно должен
	 * принимать на вход параметр $IDs - идентификаторы обрабатываемых элементов
	 *
	 * @var array
	 */
	public $group_actions = array();

	/**
	 * Идентификатор
	 */
	const ID = [
		'TYPE'           => 'NUMBER',
		'NAME'           => 'ID',
		'INDEX'          => true,
		'PRIMARY'        => true,
		'NOT_NULL'       => true,
		'AUTO_INCREMENT' => true,
		'DISABLED'       => true,
		'JOIN'           => true,
	];


	public function __construct() {
		$this->DB = Database::Instance();
		$this->code = strtolower(implode('_', array_filter(explode('\\', static::class))));
		$this->Init();
		$this->ProvideIntegrity();
	}

	/**
	 * Обеспечивает целостность данных между структурными массивами:
	 * - composition
	 * - structure
	 * - selection
	 * - primary_keys
	 */
	public function ProvideIntegrity() {

		$this->structure = [];
		$this->selection = [];
		$this->primary_keys = [];

		foreach ($this->composition as $group_code => $group) {
			foreach ($group['FIELDS'] as $field_code => $field) {
				$this->structure[$field_code] = $field;
				$this->structure[$field_code]['GROUP'] = $group_code;
				if ($field['PRIMARY']) {
					$this->primary_keys[] = $field_code;
				}
			}
		}

		$this->selection = $this->structure;

	}

	/**
	 * Инициализатор объекта.
	 * Объявляется в классе-наследнике.
	 * Может использовать parent::Init().
	 * Может использовать другие объекты для формирования структуры.
	 * Должен переопределить собственные поля:
	 * - name - имя сущности (ex: 'Подписки')
	 * - composition - структура полей для ДБ и визуализации
	 */
	public function Init() {
		$this->name = 'Элементы класса ' . static::class;
		$this->composition = [
			'SYSTEM' => [
				'NAME'   => 'Системные поля',
				'FIELDS' => [
					'ID' => [
						'TYPE'           => 'NUMBER',
						'NAME'           => 'ID',
						'INDEX'          => true,
						'PRIMARY'        => true,
						'NOT_NULL'       => true,
						'AUTO_INCREMENT' => true,
						'DISABLED'       => true,
						'JOIN'           => true,
					],
				],
			],
		];
	}

	/**
	 * Синхронизирует структуру таблицы в базе данных
	 *
	 * @param bool $strict если указан, то синхронизация удаляет лишние поля и пересортирует текущие
	 * @throws Exception
	 */
	public function Synchronize($strict = false) {
		if (empty($this->structure)) {
			throw new Exception("Synchronize of '{$this->code}' failed: structure is empty");
		}
		$tables = $this->Query("SHOW TABLES LIKE '{$this->code}'");
		//Debug($tables, '$tables');
		//Debug($this->structure, '$this->structure');
		$this->structure = $this->FormatArrayKeysCase($this->structure);

		if (empty($tables)) {
			$this->SQL = "CREATE TABLE IF NOT EXISTS `{$this->code}` \r\n";
			$rows = array();
			foreach ($this->structure as $code => $field) {
				$code = strtoupper($code);
				$field["CODE"] = $code;
				$rows[] = $this->_getStructureString($field);
			}
			if (!empty($this->primary_keys)) {
				$rows[] = "PRIMARY KEY (`" . implode("`, `", $this->primary_keys) . "`)";
			}
			$this->SQL = $this->SQL . "(" . implode(",\r\n", $rows) . ");";
			//Debug($SQL, '$SQL CREATE TABLE');
			$this->Query($this->SQL);
		} else {
			$columns = $this->Query("SHOW FULL COLUMNS FROM " . $this->code, 'Field');
			$columns = $this->FormatArrayKeysCase($columns);
			//Debug($columns, '$columns');
			$this->SQL = "ALTER TABLE `{$this->code}` \r\n";
			$rows = array();
			$last_after_code = '';
			foreach ($this->structure as $code => $field) {
				$code = strtoupper($code);
				$field["CODE"] = $code;
				$structure_string = $this->_getStructureString($field);
				if ($strict && !empty($last_after_code)) {
					$structure_string .= " AFTER {$last_after_code}";
				}
				if (!empty($columns[$code])) {
					$rows[] = "MODIFY COLUMN $structure_string";
				} elseif (!empty($field['CHANGE']) && !empty($columns[$field['CHANGE']])) {
					$rows[] = "CHANGE COLUMN `{$field['CHANGE']}` $structure_string";
					unset($columns[$field['CHANGE']]);
				} else {
					$rows[] = "ADD COLUMN $structure_string";
				}
				$last_after_code = $code;
				unset($columns[$code]);
			}
			if ($strict) {
				foreach ($columns as $code => $column) {
					$rows[] = "DROP COLUMN `{$code}`";
				}
			}
			if (!empty($this->primary_keys)) {
				$rows[] = "DROP PRIMARY KEY, ADD PRIMARY KEY (`" . implode("`, `", $this->primary_keys) . "`)";
			}
			$this->SQL = $this->SQL . implode(",\r\n", $rows) . ";";
			//Debug($this->SQL, '$SQL ALTER TABLE');
			//Log($this->SQL, '$SQL ALTER TABLE');
			$this->Query($this->SQL);
		}

		$indexes = $this->Query("SHOW INDEX FROM `{$this->code}`", 'Column_name');
		// Debug($indexes, '$indexes');
		// Log($indexes, '$indexes');
		foreach ($this->structure as $code => $field) {
			if (in_array($code, $this->primary_keys)) {
				continue;
			}
			if ($field['UNIQUE']) {
				$field['INDEX'] = true;
			}
			if ($field['INDEX'] === 'UNIQUE') {
				$field['INDEX'] = true;
				$field['UNIQUE'] = true;
			}
			$unique = ($field['UNIQUE']) ? 'UNIQUE' : '';
			$index = $indexes[$code];

			// в базе есть, в коде нет - удалить
			if (isset($index) && !$field['INDEX']) {
				$this->Query("ALTER TABLE `{$this->code}` DROP INDEX `{$code}`;");
				continue;
			}

			// в базе нет, в коде есть - добавить
			if (($field['INDEX']) && (!isset($index))) {
				$this->Query("ALTER TABLE `{$this->code}` ADD {$unique} INDEX `{$code}` (`{$code}`);");
				continue;
			}

			// в базе есть, в коде есть - уточнение уникальности индекса
			if (isset($index)) {
				if (($field['UNIQUE'] && $index['Non_unique']) || (!$field['UNIQUE'] && !$index['Non_unique'])) {
					$this->Query("ALTER TABLE `{$this->code}` DROP INDEX `{$code}`, ADD {$unique} INDEX `{$code}` (`{$code}`);");
					continue;
				}
			}
		}
	}

	/**
	 * Анализирует массив объектов $this->annexes.
	 *
	 * @return array массив строк для вставки между FROM ... JOINS
	 */
	public function _prepareAnnexes() {
		$annexes_strings = array();
		if (!empty($this->annexes)) {
			if (!is_array($this->annexes)) {
				$this->annexes = array($this->annexes);
			}
			$primary = reset($this->primary_keys);
			foreach ($this->annexes as $annex_alias => $annex) {
				$annex_primary = reset($annex->primary);
				if (is_numeric($annex_alias)) {
					$annex_alias = $annex->code;
				}
				$annexes_strings[] = "\r\n" .
					" INNER JOIN {$annex->code}" .
					" AS {$annex_alias}" .
					" ON {$this->code}.{$primary} = {$annex_alias}.{$annex_primary}";
				foreach ($annex->structure as $code => $field) {
					if (!isset($this->selection[$code])) {
						$this->selection[$code] = $field;
						$this->selection[$code]['TABLE'] = $annex_alias;
					}
				}
			}
		}
		return $annexes_strings;
	}

	/**
	 * Формирует данные для вывода страницы элементов.
	 * $arParams - массив вида:
	 * - "SORT" => сортировка
	 * - "FILTER" => фильтр
	 * - "FIELDS" => выбираемые поля
	 * - "LIMIT" => количество элементов на странице (по умолчанию — 100)
	 * - "PAGE" => номер страницы (по умолчанию — 1)
	 * - "KEY" => по какому полю нумеровать элементы (укажите FALSE чтобы нумеровать автоматически с помощью [] )
	 * - "ESCAPE" => автоматически обрабатывать поля с выбором формата text/html в HTML-безопасный вид? (по умолчанию TRUE)
	 *
	 * @param array $arParams
	 * @throws Exception
	 * @return array - массив с ключами: ELEMENTS, TOTAL, PAGER
	 */
	public function Search($arParams = array()) {

		$arParams = $this->_matchParams($arParams, array(
			"SORT"   => array('ID' => 'ASC'),
			"FILTER" => array(),
			"FIELDS" => array("**"),
			"LIMIT"  => 100,
			"PAGE"   => 1,
			"KEY"    => reset($this->primary_keys),
			"ESCAPE" => true,
		));

		$arParams["PAGE"] = max(1, intval($arParams["PAGE"]));

		$joinAnnexes = $this->_prepareAnnexes();

		// $arParams["FIELDS"] в любом случае должен быть массивом
		if (!is_array($arParams["FIELDS"])) {
			$arParams["FIELDS"] = array($arParams["FIELDS"]);
		}

		// заменяет "*" на развернутую структуру полей без глубины
		// заменяет "**" на развернутую структуру полей однократной глубины
		// заменяет "***" на развернутую структуру полей бесконечной глубины
		foreach ($arParams["FIELDS"] as $key => $field) {
			if (in_array($field, array('*', '**', '***'))) {
				unset($arParams["FIELDS"][$key]);
				$field_list = [];
				if ($field == "*") {
					$field_list = $this->GetFieldList();
				} elseif ($field == "**") {
					$field_list = $this->GetFieldList(true);
				} elseif ($field == "***") {
					$field_list = $this->GetFieldList(true, true);
				}
				$arParams["FIELDS"] = array_merge($field_list, $arParams["FIELDS"]);
				//Debug($arParams["FIELDS"], 'SEARCH * $arParams["FIELDS"]');
				break;
			}
		}

		// если программист забыл указать KEY в полях
		if (!in_array($arParams['KEY'], $arParams['FIELDS'])) {
			$arParams['FIELDS'][] = $arParams['KEY'];
		}

		$this->SQL = 'SELECT ';
		if ($arParams['LIMIT'] > 1) {
			$this->SQL .= 'SQL_CALC_FOUND_ROWS ';
		}

		list($fields, $joinTables) = $this->_prepareSelectAndJoin($arParams['FIELDS']);
		$this->SQL .= (!empty($fields)) ? implode(",\r\n", $fields) : '';
		$this->SQL .= "\r\nFROM {$this->code}\r\n";
		$this->SQL .= "\r\n" . implode("\r\n", $joinAnnexes);
		$this->SQL .= "\r\n" . implode("\r\n", $joinTables);
		$where = $this->_prepareWhere($arParams['FILTER']);
		$this->SQL .= (!empty($where)) ? "\r\nWHERE " . implode(" \r\nAND ", $where) : '';
		$order = $this->_prepareOrder($arParams['SORT']);
		$this->SQL .= (!empty($order)) ? "\r\nORDER BY " . implode(',', $order) : '';

		if ($arParams['LIMIT'] > 0) {
			$from = ($arParams['PAGE'] - 1) * $arParams['LIMIT'];
			$this->SQL .= "\r\nLIMIT {$from}, {$arParams['LIMIT']}";
		}

		$result["ELEMENTS"] = $this->Query($this->SQL, $arParams['KEY']);

		if ($arParams['LIMIT'] > 0) {
			// Битрикс предлагает следующий способ пагинации:
			// $CDBResult->NavStart($arParams['LIMIT'], false, $arParams['PAGE']);
			// проблема в том, что при таком подходе запросы в базу идут без LIMIT-а, а лишь потом
			// обрезаются нужным образом - это вызывает большие издержки на массивных таблицах
		}

		if ($arParams['LIMIT'] > 1) {
			$result["PAGER"]["TOTAL"] = (int)reset(reset($this->Query('SELECT FOUND_ROWS() as TOTAL;')));
			$result["PAGER"]["CURRENT"] = $arParams["PAGE"];
			$result["PAGER"]["LIMIT"] = $arParams["LIMIT"];
		}

		foreach ($result["ELEMENTS"] as &$row) {
			if (!$arParams['ESCAPE']) {
				// $row = htmlspecialcharsback($row);
			}
			$row = $this->FormatArrayKeysCase($row);
			$row = $this->FormatListStructure($row);
			$row = $this->FormatValues($row);
		}

		if ($arParams['LIMIT'] > 1) {
			$result["PAGER"]["SELECTED"] = count($result["ELEMENTS"]);
		}


		return $result;
	}

	/**
	 * Выбирает данные из таблицы
	 * @param array $arParams - массив вида:
	 * - "SORT" => сортировка
	 * - "FILTER" => фильтр
	 * - "FIELDS" => выбираемые поля
	 * - "LIMIT" => количество элементов на странице (по умолчанию false - все элементы)
	 * - "PAGE" => номер страницы
	 * - "KEY" => по какому полю нумеровать элементы (укажите FALSE чтобы нумеровать автоматически с помощью [] )
	 * - "ESCAPE" => автоматически обрабатывать поля с выбором формата text/html в HTML-безопасный вид? (по умолчанию TRUE)
	 *
	 * @throws Exception
	 * @return array список выбранных элементов
	 */
	public function GetList($arParams = array()) {
		$this->_controlParams($arParams, [
			'SORT',
			'FILTER',
			'FIELDS',
			'LIMIT',
			'PAGE',
			'KEY',
			'ESCAPE',
		]);
		if (!isset($arParams['LIMIT'])) {
			$arParams['LIMIT'] = false;
		}
		$data = $this->Search($arParams);
		return $data['ELEMENTS'];
	}

	/**
	 * Контролирует соответствие массива параметров заданному листу допустимых параметров.
	 * В случае несоответствия кидает ошибку.
	 *
	 * @param array $params массив контролируемых параметров
	 * @param array $keys лист допустимых параметров
	 * @throws Exception Переданы некорректные параметры ...
	 */
	private function _controlParams($params = array(), $keys = array()) {
		$errors = [];
		foreach ($params as $key => $value) {
			if (!in_array($key, $keys)) {
				$errors[] = $key;
			}
		}
		if (!empty($errors)) {
			throw new Exception("Переданы некорректные параметры: [" . implode(", ", $errors) . "], допускаются только следующие параметры: [" . implode(", ", $keys) . "]");
		}
	}

	/**
	 * Выбирает первый элемент по фильтру. Можно указать поля и сортировку.
	 *
	 * @param array|string|int $filter фильтр или ID элемента
	 * @param array|string $fields выбираемые поля
	 * @param array $sort сортировка
	 * @return array|false ассоциативный массив, представляющий собой элемент
	 */
	public function Read($filter = array(), $fields = array("**"), $sort = array()) {
		if (!is_array($filter)) {
			$filter = array(reset($this->primary_keys) => $filter);
		}
		$arParams = array(
			"FILTER" => $filter,
			"FIELDS" => $fields,
			"SORT"   => $sort,
			"LIMIT"  => 1,
		);
		$data = $this->Search($arParams);
		$element = reset($data["ELEMENTS"]);
		return $element;
	}

	/**
	 * Проверяет присутствует ли элемент с указанным идентификатором в таблице
	 *
	 * @param int $ID идентификатор элемента
	 * @return boolean true - если присутствует, false - если не присутствует
	 */
	public function Present($ID) {
		return (bool)$this->Read(array(reset($this->primary_keys) => $ID), $this->primary_keys);
	}

	/**
	 * Выбирает список идентификаторов\значений указанной колонки.
	 *
	 * @param array|mixed $filter фильтр или ID элемента
	 * @param array $sort сортировка (не обязательно)
	 * @param string $field символьный код выбираемой колонки (не обязательно, по умолчанию - идентификатор)
	 * @return array массив идентификаторов элементов
	 */
	public function Select($filter = array(), $sort = array(), $field = null) {
		if (!is_array($filter)) {
			$filter = array(reset($this->primary_keys) => $filter);
		}
		if (empty($field)) {
			$field = reset($this->primary_keys);
		}
		$elements = $this->GetList(array(
			"FILTER" => $filter,
			"FIELDS" => array($field),
			"SORT"   => $sort,
		));
		$keys = array();
		foreach ($elements as $element) {
			$keys[] = $element[$field];
		}
		return $keys;
	}

	/**
	 * Анализирует значение на наличие информации.
	 * - 0 - информация присутствует
	 * - '0' - информация присутствует
	 * - false - информация присутствует
	 * - null - информация отсутствует
	 * - empty array() - информация отсутствует
	 * - '' - информация отсутствует
	 * - в других случаях - информация присутствует
	 *
	 * Отсутствие информации в переменных PHP эквивалетно в SQL значению NULL:
	 * PHP null == PHP empty array() == PHP '' == SQL NULL == SQL ''
	 *
	 * @param mixed $value значение
	 * @return boolean флаг наличия информации
	 */
	private function _hasInformation($value) {
		if ($value === 0 || $value === '0' || $value === false) {
			return true;
		}
		if (empty($value)) {
			return false;
		}
		return true;
	}

	/**
	 * Формирует часть SQL запроса "SET ..., ..., ..." для вставки\изменения.
	 * Если значение пустая строка или null - возвращает "... = NULL".
	 * Если значение пустая строка или null, а поле NOT_NULL - ошибка.
	 *
	 * @param string $code код поля
	 * @param mixed $value значение поля
	 * @return string подстрока для SQL
	 * @throws Exception Поле ... не может быть пустым
	 */
	private function _prepareSet($code, $value) {
		$hasInformation = $this->_hasInformation($value);
		if (($this->structure[$code]['NOT_NULL'] || $this->structure[$code]['TYPE'] == 'BOOL') && !$hasInformation) {
			throw new Exception("Поле '{$this->structure[$code]['NAME']}' не может быть пустым");
		}
		if ($hasInformation) {
			$value = $this->_formatFieldValue($code, $value);
			$set = "`{$code}` = '{$value}'";
		} else {
			$set = "`{$code}` = NULL";
		}
		return $set;
	}

	/**
	 * Проверяет массив полей, передаваемый в Create и Update методы.
	 * Проверка идет независимо от идентификатора (ID).
	 * Переопределяется в классах-наследниках для реализации логики контроля за полями.
	 *
	 * @param array $fields поля
	 * @return array поля
	 */
	public function ControlFields($fields = array()) {
		return $fields;
	}

	/**
	 * Создает новую строку в таблице
	 *
	 * @param array $fields ассоциативный массив полей для новой строки
	 * @return int идентификатор созданной записи
	 * @throws Exception
	 */
	public function Create($fields) {

		$fields = $this->ControlFields($fields);

		if (empty($fields)) {
			$this->SQL = "INSERT INTO {$this->code} VALUES ()";
			return $this->Query($this->SQL);
		}

		$this->SQL = "INSERT INTO {$this->code} SET ";

		foreach ($this->structure as $code => $field) {
			if ($field['NOT_NULL'] && !($field['AUTO_INCREMENT'] || $field['DEFAULT'])) {
				if (!$this->_hasInformation($fields[$code])) {
					throw new Exception("Не указано обязательное поле '{$field['NAME']}' [{$code}]");
				}
			}
		}

		$rows = array();
		foreach ($this->structure as $code => $field) {
			if (array_key_exists($code, $fields)) {
				$rows[] = $this->_prepareSet($code, $fields[$code]);
			}
		}
		$this->SQL .= implode(", ", $rows);
		$ID = $this->Query($this->SQL);
		return $ID;
	}

	/**
	 * Изменяет значения указанных полей.
	 * Автоматически подставляет актуальные значения в поля UPDATE_DATE и UPDATE_BY, если они не указаны.
	 * Установите этим полям null для того чтобы SCRUD не изменял значения этих полей.
	 *
	 * @param int|array $ids идентификатор/идентификаторы изменяемых записей
	 * @param array $fields ассоциативный массив изменяемых полей
	 * @return bool true - в случае успешного обновления полей, false - в случае если поля не были указаны
	 * @throws Exception Нет информации для обновления
	 * @throws Exception Поле ... не может быть пустым
	 */
	public function Update($ids = array(), $fields = array()) {

		$fields = $this->ControlFields($fields);

		if (empty($fields)) {
			return false;
		}
		if (!is_array($ids)) {
			$ids = array($ids);
		}
		if (empty($ids)) {
			return false;
		}

		$this->SQL = "UPDATE {$this->code} SET ";

		$rows = array();
		foreach ($this->structure as $code => $field) {
			if (array_key_exists($code, $fields)) {
				$rows[] = $this->_prepareSet($code, $fields[$code]);
			}
		}
		if (empty($rows)) {
			return false;
		}
		$this->SQL .= implode(",\r\n", $rows);
		$primary = reset($this->primary_keys);
		$this->SQL .= "\r\n WHERE {$primary} IN ('" . implode("', '", $ids) . "')";
		$this->Query($this->SQL);
		return true;
	}

	/**
	 * Удаляет строку или строки из таблицы
	 *
	 * @param int|array $ids Идентификаторы удаляемых записей
	 * @return boolean Результат выполнения
	 */
	public function Delete($ids = array()) {
		if (!is_array($ids)) {
			$ids = array($ids);
		}
		if (empty($ids)) {
			return false;
		}
		$primary = reset($this->primary_keys);
		$this->SQL = "DELETE FROM {$this->code} WHERE {$primary} IN ('" . implode("', '", $ids) . "')";
		$this->Query($this->SQL);
		return true;
	}


	/**
	 * Преобразует поле из структуры в строку, используемую в MySQL запросах при построении таблиц.
	 * Например: `NAME` varchar(255) NULL COMMENT 'Имя'
	 *
	 * @param array $field ассоциативный массив, описывающий поле
	 * @return string
	 * @throws Exception если у поля неизвестный тип
	 */
	protected function _getStructureString($field = array()) {

		$code = strtoupper($field["CODE"]);
		if (!isset($this->structure[$code])) {
			throw new Exception("Unknown field code: '{$code}' in class `" . get_class($this) . "`");
		}

		if ($field['TYPE'] === 'ENUM' || $field['TYPE'] === 'SET') {
			$type = $this->TYPES[$field['TYPE']] . '("' . implode('", "', array_keys($field['VALUES'])) . '")';
		} elseif (empty($field['TYPE'])) {
			$type = reset($this->TYPES);
		} elseif (isset($this->TYPES[$field['TYPE']])) {
			$type = $this->TYPES[$field['TYPE']];
		} else {
			throw new Exception("Неизвестный тип поля [{$this->code}.{$field["CODE"]}] - [{$field['TYPE']}]");
		}

		$null = ($field["NOT_NULL"]) ? "NOT NULL" : "NULL";
		if ($field['TYPE'] == 'BOOL') {
			$null = "NOT NULL";
		}

		$default = "";
		if ($field['DEFAULT']) {
			if (is_array($field['DEFAULT'])) {
				$field['DEFAULT'] = implode(',', $field['DEFAULT']);
			}
			$default = "DEFAULT '{$field['DEFAULT']}'";
		}

		$auto_increment = ($field["AUTO_INCREMENT"]) ? "AUTO_INCREMENT" : "";

		$comment = ($field["NAME"]) ? " COMMENT '{$field["NAME"]}'" : "";

		$string = "`{$field["CODE"]}` $type $null $default $auto_increment $comment";

		return $string;
	}

	/**
	 * Формирует массив параметров на основе входящих параметров и дефолтных параметров.
	 * Выдает массив, ключи которого состоят из дефолтных параметров, а значения - из входящих по возможности.
	 * @param array $arParams - входящие параметры
	 * @param array $defParams - дефолтные параметры
	 * @return array - инициализированные параметры
	 */
	protected function _matchParams($arParams, $defParams) {
		foreach ($defParams as $key => $param) {
			if (isset($arParams[$key])) {
				$defParams[$key] = $arParams[$key];
			}
		}
		return $defParams;
	}

	/**
	 * Создает массивы для выборки и джоинов
	 *
	 * @param array $fields поля для выборки
	 * @param string $prefix какой добавить префикс
	 * @return array массив из двух элементов: 1 - Часть выражения после Select, 2 - Часть выражений LEFT JOIN
	 * @throws Exception
	 */
	protected function _prepareSelectAndJoin($fields, $prefix = "") {
		$select = array();
		$join = array();
		foreach ($fields as $code => $content) {
			if (!is_array($content)) {
				$code = strtoupper($content);
				$is_external = false;
				$subfields = false;
			} else {
				$code = strtoupper($code);
				$is_external = true;
				$subfields = $content;
			}
			unset($content);

			if ($code === '*') {
				list($addSelect, $addJoin) = $this->_prepareSelectAndJoin($this->GetFieldList(), $prefix);
				$select = array_merge($select, $addSelect);
				$join = array_merge($join, $addJoin);
				continue;
			}

			if (empty($this->selection[$code])) {
				throw new Exception("Unknown field code: '{$code}' in table '{$this->code}'");
			}
			$field = $this->selection[$code];

			if (isset($field['TABLE'])) {
				$table = $field['TABLE'];
			} else {
				$table = $prefix . $this->code;
			}

			if ($field['LINK'] and $is_external) {
				/** @var self $external */
				$external = $field['LINK']::Instance();
				if (!in_array(self::class, class_parents($external))) {
					throw new Exception("External class '{$field['LINK']}' specified in the field '{$code}' is not child of " . self::class);
				}

				$external_prefix = $prefix . strtoupper($code) . "__";

				$join[] = "LEFT JOIN {$external->code} AS {$external_prefix}{$external->code} ON {$table}.{$code} = {$external_prefix}{$external->code}.ID";

				list($addSelect, $addJoin) = $external->_prepareSelectAndJoin($subfields, $external_prefix);
				$select = array_merge($select, $addSelect);
				$join = array_merge($join, $addJoin);
			} else {
				$select[] = "{$table}.`{$code}` as `{$prefix}{$code}`";
			}
		}
		return array($select, $join);
	}

	/**
	 * Подготавливает часть SQL запроса WHERE из фильтра.
	 * Значения в фильтре могут быть:
	 * - *значение* - фильтр будет формироваться по всем правилам
	 * - 0 (zero) - фильтр будет формироваться по всем правилам
	 * - "" (empty string) - фильтр не будет формироваться
	 * - null - фильтр сформируется в "[NOT] IS NULL"
	 * - лист значений - сформируется набор ".. IN (...) [OR ... [NOT] IS NULL]"
	 *
	 * Ключи в фильтре могут быть:
	 * - <символьный код поля>
	 * - <символьный код поля типа ссылка>.<символьный код поля внешнего объекта>
	 * - <символьный код поля типа ссылка>.<символьный код поля типа ссылка>.<символьный код поля внешнего объекта>
	 * - ...
	 * - OR или AND
	 *
	 * Ключи поддерживают префиксы:
	 * -  =   - равно (по умолчанию)
	 * -  !   - не равно
	 * -  <>  - не равно
	 * -  >   - больше либо равно
	 * -  <   - меньше либо равно
	 * -  >>  - строго больше
	 * -  <<  - строго меньше
	 * -  %   - LIKE
	 * -  ~   - LIKE
	 *
	 * @param array $filter ассоциативный массив фильтров
	 * - ключ - условие и имя поля
	 * - значение - значение или лист значений
	 * @return array массив строк, представляющих собой SQL-условия, которые следует объеденить операторами AND или OR
	 * @throws Exception
	 */
	protected function _prepareWhere($filter) {
		if (empty($filter)) {
			return array('1');
		}

		$where = array();

		foreach ($filter as $filter_key => $values) {
			if ($values === '') {
				continue;
			}

			// вложенные операторы AND и OR
			if ($filter_key === 'AND' || $filter_key === 'OR') {
				if (!is_array($values)) {
					throw new Exception("При использовании в фильтре вложенных операторов AND и OR значение должно быть массивом условий");
				}
				$where[] = '(' . implode(" \n\r{$filter_key} ", $this->_prepareWhere($values)) . ')';
				continue;
			}

			// вычисление ключа
			// possible $key : >>FIELD
			// possible $key : >>EXTERNAL_FIELD.FIELD
			// possible $key : >>EXTERNAL_FIELD.EXTERNAL_FIELD.FIELD
			// possible $key : ...
			preg_match('/^(?P<operator>\W*)(?P<field_path>[a-zA-Z_\.]+)/', $filter_key, $matches);
			$operator = $matches['operator']; // >>
			$field_path = $matches['field_path'];

			$result = $this->_treatFieldPath($field_path);
			/** @var self $object */
			$object = $result['OBJECT'];
			$table = $result['TABLE'];
			$code = $result['CODE'];

			// проверка значений
			$null = false; // значения содержат NULL ?
			if (!is_array($values)) {
				$values = array($values);
			}

			if (count($values) === 0) {
				$null = true;
			}

			foreach ($values as $key => $value) {
				if (is_null($value)) {
					$null = true;
					unset($values[$key]);
				} else {
					$values[$key] = $object->_formatFieldValue($code, $values[$key]);
				}
			}

			$conditions = array();
			if ($null) {
				switch ($operator) {
					case '!':
					case '<>':
						$conditions['null'] = ' IS NOT NULL';
						break;
					default:
						$conditions['null'] = ' IS NULL';
						break;
				}
			}
			if (count($values) == 1) {
				$value = reset($values);
				switch ($operator) {
					case '>>':
						$conditions['>>'] = '>"' . $value . '"';
						break;
					case '!':
					case '<>':
						$conditions['<>'] = '<>"' . $value . '"';
						break;
					case '<<':
						$conditions['<<'] = '<"' . $value . '"';
						break;
					case '<':
						$conditions['<'] = '<="' . $value . '"';
						break;
					case '>':
						$conditions['>'] = '>="' . $value . '"';
						break;
					case '%':
					case '~':
						$conditions['%'] = ' LIKE "%' . $value . '%"';
						break;
					default:
						$conditions['='] = '="' . $value . '"';
						break;
				}
			} elseif (count($values) > 1) {
				if (!empty($values)) {
					$conditions[] = ' IN ("' . implode('", "', $values) . '")';
				}
			}

			foreach ($conditions as $key => $condition) {
				if (($key === '%') && ($this->structure[$code]['TYPE'] === 'DATETIME')) {
					$data = date('Y-m-d', strtotime($value));
					$conditions[$key] = "DATE({$table}.{$code}) = '{$data}'";
					continue;
				}
				$conditions[$key] = $table . "." . $code . $condition;
			}

			$conditions = "(" . implode(' OR ', $conditions) . ")";
			$where[] = $conditions;
		}
		return $where;
	}

	/**
	 * Обрабатывает путь к полю. Вычисляет объект для обработки поля, псевдоним таблицы и код поля.
	 *
	 * @param string $field_path мнемонический путь к полю, например: 'EXTERNAL_FIELD.EXTERNAL_FIELD.FIELD'
	 * @return array Структура с ключами:
	 * - OBJECT - объект-наследник SCRUD для обработки поля
	 * - TABLE - псевдоним для таблицы (AS)
	 * - CODE - код поля
	 * @throws Exception Unknown external field code
	 * @throws Exception Field is not external
	 */
	protected function _treatFieldPath($field_path) {
		$path = explode(".", $field_path); // EXTERNAL_FIELD.EXTERNAL_FIELD.FIELD
		$code = array_pop($path); // FIELD
		if (!empty($path)) {
			/** @var self $object */
			$object = null;
			$table = '';
			$structure = &$this->selection;
			foreach ($path as $external) {
				if (empty($structure[$external])) {
					throw new Exception("Unknown external field code: '{$external}'");
				}
				if (empty($structure[$external]["LINK"])) {
					throw new Exception("Field is not external: '{$external}'");
				}
				$object = $structure[$external]["LINK"]::Instance();
				$table .= $external . "__";
				$structure = &$object->structure;
			}
			$table = $table . $object->code;
			unset($structure);
		} else {
			$object = $this;
			$table = $this->code;
			if (!empty($this->selection[$code]['TABLE'])) {
				$table = $this->selection[$code]['TABLE'];
			}
		}
		return array(
			"OBJECT" => $object,
			"TABLE"  => $table,
			"CODE"   => $code,
		);
	}

	/**
	 * Подготавливает часть SQL запроса ORDER BY
	 *
	 * @param array $array Массив фильтра SORT
	 * @return array Массив с ключами ORDER BY
	 */
	protected function _prepareOrder($array) {
		$order = array();
		foreach ($array as $field_path => $sort) {
			$result = $this->_treatFieldPath($field_path);
			$table = $result['TABLE'];
			$code = $result['CODE'];
			$order[] = "{$table}.`{$code}` {$sort}";
		}
		return $order;
	}

	/**
	 * Приводит значение в соответствие формату поля.
	 * - Числовые - приводит к числу нужного формата.
	 * - Строковые - обрезает по нужному размеру.
	 * - Списковые - подставляет корректное значение.
	 * - Битовые - подставляет Y|N.
	 * - Даты - подставляет дату в формате ISO8601.
	 * - Файловые - сохраняет файл в b_file, выдает его идентификатор.
	 *
	 * @param string $code код поля
	 * @param mixed $value значение
	 * @return mixed приведенное к формату значение
	 * @throws Exception Unknown field code
	 */
	protected function _formatFieldValue($code, $value) {
		$code = strtoupper($code);
		if (!isset($this->selection[$code])) {
			throw new Exception("Неизвестный код поля: '{$code}'");
		}
		$field = $this->selection[$code];

		if (!$this->_hasInformation($value)) {
			return null;
		}

		if ($field['ARRAY'] === 'JSON') {
			if (is_array($value)) {
				$value = json_encode($value, JSON_UNESCAPED_UNICODE);
			}
		}

		switch ($field['TYPE']) {
			case 'DATE':
			case 'DATETIME':
				if (is_numeric($value)) {
					$value = date(\DateTime::ISO8601, $value);
				} else {
					$value = date(\DateTime::ISO8601, strtotime($value));
				}
				break;
			case 'NUMBER':
			case 'LINK':
				$value = intval($value);
				break;
			case 'FLOAT':
				$value = floatval($value);
				break;
			case 'ENUM':
				if (!in_array($value, array_keys($field['VALUES']))) {
					$value = null;
				}
				if (empty($value)) {
					if ($field['NOT_NULL']) {
						if (!empty($field['DEFAULT'])) {
							$value = $field['DEFAULT'];
						} else {
							$value = reset(array_keys($field['VALUES']));
						}
					}
				}
				break;
			case 'SET':
				$values = $value;
				if (!is_array($values)) {
					$values = explode(',', $values);
				}
				$possible_values = array_keys($field['VALUES']);
				$scraps = array();
				foreach ($values as $scrap) {
					if (in_array($scrap, $possible_values)) {
						$scraps[] = $scrap;
					}
				}
				$value = implode(',', $scraps);
				break;
			case 'BOOL':
				if ($value === 'Y' || $value === 'N') {
					// $value = $value;
				} elseif ($value === false) {
					$value = 'N';
				} elseif ($value === true) {
					$value = 'Y';
				} else {
					$value = 'N';
				}
				break;
			case 'TEXT':
				// $value = $value;
				break;
			case 'FILE':
				if (is_array($value)) {
					$value = $field['LINK']::I()->Create($value);
				} else {
					$value = intval($value);
				}
			case 'STRING':
			default:
				$value = substr($value, 0, 250);
				$value = preg_replace('#\s+#', ' ', $value);
				$value = trim($value);
				break;
		}
		$value = $this->DB->Escape($value);
		return $value;
	}


	/**
	 * Повышает регистр ключей массива на первом уровне вложенности
	 *
	 * @param array $input Входной массив
	 * @return array Выходной массив
	 */
	public function FormatArrayKeysCase($input) {
		return array_change_key_case($input, CASE_UPPER);
	}

	/**
	 * Выбирает все поля таблицы для подстановки в параметр FIELDS метода Search
	 * GetFieldList() - вернет структуру без полей связанных элементов
	 * GetFieldList(true) - вернет структуру с минимумом полей связанных элементов
	 * GetFieldList(true, true) - вернет структуру с максимумом полей связанных элементов
	 *
	 * @param bool $medium Выбрать связанные поля?
	 * @param bool $full Выбрать все возможные поля?
	 * @param bool $join Указывает на то, что метод используется для сбора полей в
	 * присоединяемую таблицу (собираются не все поля, а лишь те, у которых JOIN == true)
	 *
	 * @return array поля таблицы для подстановки в параметр FIELDS
	 */
	public function GetFieldList($medium = false, $full = false, $join = false) {
		$list = array();
		foreach ($this->selection as $code => $field) {

			if ($medium) {
				if ($field['LINK']) {
					if (class_exists($field["LINK"])) {
						/** @var self $external */
						$external = $field["LINK"]::Instance();
						$list[$code] = $external->GetFieldList($full, $full, true);
						continue;
					}
				}
			}

			if (!$join || $field['JOIN']) {
				$list[$code] = $code;
				continue;
			}

		}
		return $list;
	}

	/**
	 * Преобразует одномерный ассоциативный массив с ключами типа ["EXTERNAL__NAME"]
	 * древовидный ассоциативный массив с ключами типа ["EXTERNAL"]["NAME"].
	 * Поддерживается неограниченная вложенность.
	 *
	 * @param array $list одномерный ассоциативный массив
	 * @param string $separator разделитель
	 * @return array древовидный ассоциативный массив
	 */
	public function FormatListStructure($list, $separator = "__") {
		$element = array();
		foreach ($list as $code => $value) {
			$codes = explode($separator, $code);
			$link = &$element;
			foreach ($codes as $path) {
				$link = &$link[$path];
			}
			$link = $value;
		}
		return $element;
	}

	/**
	 * Преобразует в элементе значения типа SET из текстовых строк с разделителями "," в массивы.
	 * Например: "1,2,4" => array("1", "2", "4").
	 *
	 * Добавляет к каждому значению типа список (L) два ключа - ключ и визуализация.
	 * Например для поля STATUS (L) равного 'NEW':
	 * - [STATUS|KEY] => NEW
	 * - [STATUS|VALUE] => Новый
	 *
	 * @param array $element элемент
	 * @return array элемент с преобразованными значениями
	 */
	public function FormatValues($element) {
		if (!is_array($element)) {
			return $element;
		}
		foreach ($element as $code => $value) {
			$field = $this->selection[$code];
			if ($field['LINK']) {
				/** @var self $external */
				$external = $field["LINK"]::Instance();
				$element[$code] = $external->FormatValues($value);
				// метод должен остаться публичным, так как вызывается в контексте других
				// объектов для доступа изнутри этих объектов к их структуре ($this->selection)
			}
			if ($field['TYPE'] === 'ENUM') {
				$element["$code|VALUE"] = $field['VALUES'][$value];
			}
			if ($field['TYPE'] === 'SET') {
				$element["$code"] = explode(",", $value);
				$element["$code|VALUES"] = array();
				foreach ($element["$code"] as $key) {
					$element["$code|VALUES"][$key] = $field['VALUES'][$key];
				}
			}
			if ($field['ARRAY'] === 'JSON') {
				$element["$code|JSON"] = $element[$code];
				$element["$code"] = json_decode($element[$code]);
			}
		}
		return $element;
	}

	/**
	 * @return string время в формате ISO8601
	 */
	public static function Now() {
		return date(\DateTime::ISO8601, time());
	}


	/**
	 * Удаляет все данные из таблицы не затрагивая структуру.
	 */
	public function Truncate() {
		$this->Query("TRUNCATE TABLE `{$this->code}`");
	}

	/**
	 * Исполняет SQL-запрос, не останавливая выполнение в случае ошибки.
	 * Вместо этого кидает исключение (с текстом ошибки для администраторов).
	 *
	 * @param string $SQL SQL-запрос
	 * @param string $key код колонки значения которой будут использованы как ключ в результирующем массиве (не обязательно)
	 * @return array результат выполнения
	 * @throws ExceptionSQL
	 */
	public function Query($SQL, $key = null) {
		try {
			return $this->DB->Query($SQL, $key);
		} catch (ExceptionSQL $ExceptionSQL) {
			if (substr($ExceptionSQL->GetMessage(), 0, 14) === 'Unknown column') {
				$this->Synchronize();
				return $this->DB->Query($SQL, $key);
			} else {
				throw $ExceptionSQL;
			}
		}
	}

	/**
	 * В текстовом виде печатает композицию объекта.
	 * Используется для составления документации.
	 */
	public function PrintComposition() {
		foreach ($this->composition as $group) {
			echo "\r\n" . $group['NAME'];
			foreach ($group['FIELDS'] as $field) {
				echo "\r\n - " . $field['NAME'];
				if ($field['VALUES']) {
					foreach ($field['VALUES'] as $code => $name) {
						echo "\r\n -- " . $code . " - " . $name;
					}
				}
			}
		}
	}

	/**
	 * Извлекает набор структуры, состоящий из указанных полей.
	 * Используется в компонентах для подготовки форм.
	 *
	 * @param array $codes линейный массив символьных кодов полей
	 * @return array структура
	 */
	public function ExtractStructure($codes = array()) {
		$structure = array();
		foreach ($codes as $code) {
			if (isset($this->structure[$code])) {
				$structure[$code] = $this->structure[$code];
			}
		}
		return $structure;
	}

	public function GetAdminUrl() {
		$name = get_called_class();
		$name = str_replace('\\', '/', $name);
		$name = strtolower($name);
		return "/admin/{$name}.php";
	}

}
