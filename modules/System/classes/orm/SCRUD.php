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
 * Чтобы создать новый источник данных нужно:
 * - создать класс-наследник от SCRUD
 * - переопределить метод Init, определить в нем структуру данных $this->structure
 * - однократно запустить $this->Synchronize(), например в установщике модуля
 * - при необходимости переопределить другие методы (например проверки целостности при создании или редактировании записи)
 * - при необходимости добавить дополнительный функционал, описывающий бизнес-логику работы с данными
 */
abstract class SCRUD extends Instanceable {

	/** @var string последний выполненный SQL-запрос (для отладки) */
	public $SQL;
	/** @var string части от последнего SQL-запроса (для отладки) */
	public $parts;
	/** @var \System\Database коннектор базы данных */
	protected $DB;

	/** @var string имя источника данных или таблицы или сущностей в ней */
	public $name;
	/** @var string символьный код таблицы, формируется автоматически, возможно переопределить */
	public $code;

	/** @var array массив полей базы данных */
	public $structure = [];
	/** @var Type[] массив полей базы данных */
	public $types = [];
	/** @var array массив групп полей базы данных */
	public $groups = [];
	/** @var array композиция групп полей и полей базы данных, формируется автоматически на основе $this->structure и $this->groups */
	public $composition = [];
	/** @var array массив первичных ключей, формируется автоматически */
	public $keys = [];

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
		'VITAL'          => true,
	];


	public function __construct() {
		/** @var \System\Database $DB */
		$DB = Database::Instance();
		$this->DB = $DB;
		$this->code = strtolower(implode('_', array_filter(explode('\\', static::class))));
		$this->Init();
		$this->ProvideIntegrity();
	}

	/**
	 * Returns the only one single primary key if it exist.
	 * Otherwise throws exception.
	 *
	 * @return string
	 * @throws Exception Single primary key required
	 */
	public function key() {
		if (count($this->keys) === 1) {
			return reset($this->keys);
		}
		throw new Exception("Single primary key required for " . static::class);
	}

	/**
	 * Обеспечивает целостность данных между структурными массивами:
	 * - structure
	 * - types
	 * - groups
	 * - composition
	 * - keys
	 */
	public function ProvideIntegrity() {

		$this->composition = [];
		$this->keys = [];

		foreach ($this->structure as $code => $field) {
			if ($field['PRIMARY']) {
				$this->keys[] = $code;
			}
			if (empty($field['GROUP'])) {
				$this->structure[$code]['GROUP'] = 'OUTSIDE';
				$this->groups['OUTSIDE'] = $this->groups['OUTSIDE'] ?: '-';
				continue;
			}
			if (empty($this->groups[$field['GROUP']])) {
				$this->groups[$field['GROUP']] = "[{$field['GROUP']}]";
			}
		}

		foreach ($this->groups as $group_code => $group_name) {
			$this->composition[$group_code] = [
				'NAME'   => $group_name,
				'FIELDS' => [],
			];
			foreach ($this->structure as $code => $field) {
				if ($field['GROUP'] === $group_code) {
					$this->composition[$group_code]['FIELDS'][$code] = $field;
				}
			}
		}

		if (empty($this->keys)) {
			throw new Exception("Primary keys required for " . static::class);
		}

		// Auto-completion of LINK attributes without namespaces
		foreach ($this->structure as $code => &$info) {
			if (!empty($info['LINK']) && !class_exists($info['LINK'])) {
				$link_namespace = (new \ReflectionClass($this))->getNamespaceName();
				$link = $link_namespace . '\\' . $info['LINK'];
				if (class_exists($link)) {
					$info['LINK'] = $link;
				} else {
					throw new Exception("Valid class name required for LINK of field '{$code}' of class " . static::class);
				}
			}
		}

		// Initialisation of $this->types
		foreach ($this->structure as $code => &$info) {
			$info['CODE'] = $code;
			$this->types[$code] = FactoryType::I()->Get($info);
		}
	}

	/**
	 * Инициализатор объекта, объявляется в классе-наследнике.
	 * Может использовать другие объекты для формирования структуры.
	 * Должен определить собственные поля: name, structure
	 * Может определить собственные поля: groups, code
	 */
	public function Init() {

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
		$this->structure = $this->FormatArrayKeysCase($this->structure);

		if (empty($tables)) {
			$this->SQL = "CREATE TABLE IF NOT EXISTS `{$this->code}` \r\n";
			$rows = [];
			foreach ($this->structure as $code => $field) {
				try {
					$rows[] = $this->types[$code]->GetStructureString();
				} catch (\Exception $error) {
					continue;
				}
			}
			if (!empty($this->keys)) {
				$rows[] = "PRIMARY KEY (`" . implode("`, `", $this->keys) . "`)";
			}
			$this->SQL = $this->SQL . "(" . implode(",\r\n", $rows) . ");";
			$this->Query($this->SQL);
		} else {
			$columns = $this->Query("SHOW FULL COLUMNS FROM " . $this->code, 'Field');
			$columns = $this->FormatArrayKeysCase($columns);
			$this->SQL = "ALTER TABLE `{$this->code}` \r\n";
			$rows = [];
			$last_after_code = '';
			foreach ($this->structure as $code => $field) {
				try {
					$structure_string = $this->types[$code]->GetStructureString();
				} catch (\Exception $error) {
					continue;
				}
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
			if (!empty($this->keys)) {
				$rows[] = "DROP PRIMARY KEY, ADD PRIMARY KEY (`" . implode("`, `", $this->keys) . "`)";
			}
			$this->SQL = $this->SQL . implode(",\r\n", $rows) . ";";
			$this->Query($this->SQL);
		}

		$indexes = $this->Query("SHOW INDEX FROM `{$this->code}`", 'Column_name');
		foreach ($this->structure as $code => $field) {
			if (in_array($code, $this->keys)) {
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

	public function CompileSQLSelect(array $parts) {
		$SQL = [];
		$SQL[] = 'SELECT';
		if ($parts['LIMIT']) $SQL[] = 'SQL_CALC_FOUND_ROWS';
		$SQL[] = implode(",\r\n", $parts['SELECT']);
		$SQL[] = "FROM {$this->code}";
		$SQL[] = implode("\r\n", $parts['JOIN']);
		if ($parts['WHERE']) $SQL[] = "WHERE " . implode("\r\nAND ", $parts['WHERE']);
		if ($parts['GROUP']) $SQL[] = "GROUP BY " . implode(", ", $parts['GROUP']);
		if ($parts['ORDER']) $SQL[] = "ORDER BY " . implode(", ", $parts['ORDER']);
		if ($parts['LIMIT']) $SQL[] = "LIMIT {$parts['LIMIT']['FROM']}, {$parts['LIMIT']['COUNT']}";
		return implode("\r\n", $SQL);
	}

	/**
	 * Формирует данные для вывода страницы элементов.
	 * $arParams - массив вида:
	 * - 'SORT' -- сортировка
	 * - 'FILTER' -- пользовательский фильтр (можно передавать небезопасные данные)
	 * - 'CONDITIONS' -- произвольные SQL условия для фильтрации
	 * - 'FIELDS' -- выбираемые поля
	 * - 'LIMIT' -- количество элементов на странице (по умолчанию — 100)
	 * - 'PAGE' -- номер страницы (по умолчанию — 1)
	 * - 'KEY' -- по какому полю нумеровать элементы (укажите FALSE чтобы нумеровать автоматически с помощью [] )
	 * - 'ESCAPE' -- автоматически обрабатывать поля с выбором формата text/html в HTML-безопасный вид? (по умолчанию TRUE)
	 * - 'GROUP' -- группировка
	 *
	 * @param array $arParams
	 * @throws Exception
	 * @return array - массив с ключами: ELEMENTS, TOTAL, PAGER
	 */
	public function Search($arParams = []) {
		$defParams = [
			'SORT'       => [],
			'FILTER'     => [],
			'CONDITIONS' => [],
			'FIELDS'     => ['*@@'],
			'LIMIT'      => 100,
			'PAGE'       => 1,
			'ESCAPE'     => true,
			'GROUP'      => [],
		];
		try {
			$defParams['KEY'] = $this->key();
			$defParams['GROUP'] = [$this->key()];
			$defParams['SORT'] = [$this->key() => 'DESC'];
		} catch (Exception $error) {
			$defParams['KEY'] = null;
		}

		$arParams = $this->_matchParams($arParams, $defParams);

		$arParams["PAGE"] = max(1, intval($arParams["PAGE"]));

		$arParams['FIELDS'] = $this->ExplainFields($arParams['FIELDS']);

		// если в полях нет ключевого поля - добавить его в начало
		if (!empty($arParams['KEY']) and !in_array($arParams['KEY'], $arParams['FIELDS'])) {
			$arParams['FIELDS'][$arParams['KEY']] = $arParams['KEY'];
		}

		// compile parts
		$this->parts = [
			'SELECT' => [],
			'JOIN'   => [],
			'WHERE'  => [],
			'ORDER'  => [],
			'GROUP'  => [],
			'LIMIT'  => [],
		];

		$answer = $this->PrepareSelectAndJoinByFields($arParams['FIELDS']);
		$this->parts['SELECT'] += $answer['SELECT'];
		$this->parts['JOIN'] += $answer['JOIN'];

		$answer = $this->PrepareWhereAndJoinByFilter($arParams['FILTER']);
		$this->parts['WHERE'] += $answer['WHERE'];
		$this->parts['JOIN'] += $answer['JOIN'];

		$arParams['CONDITIONS'] = is_array($arParams['CONDITIONS']) ? $arParams['CONDITIONS'] : [$arParams['CONDITIONS']];
		$this->parts['WHERE'] += $arParams['CONDITIONS'];

		$this->parts['GROUP'] += $this->_prepareGroup($arParams['GROUP']);

		$this->parts['ORDER'] += $this->_prepareOrder($arParams['SORT']);
		if ($arParams['LIMIT'] > 0) {
			$this->parts['LIMIT'] = [
				'FROM'  => ($arParams['PAGE'] - 1) * $arParams['LIMIT'],
				'COUNT' => $arParams['LIMIT'],
			];
		}

		$this->SQL = $this->CompileSQLSelect($this->parts);

		$result["ELEMENTS"] = $this->Query($this->SQL, $arParams['KEY']);

		foreach ($result["ELEMENTS"] as &$row) {
			$row = $this->FormatArrayKeysCase($row);
			$row = $this->FormatListStructure($row);
			$row = $this->FormatOutputValues($row);
			if ($arParams['ESCAPE']) {
				array_walk_recursive($row, function (&$value) {
					$value = htmlspecialchars($value);
				});
			}
		}

		if ($arParams['LIMIT'] > 1) {
			$result['PAGER']['TOTAL'] = (int)reset(reset($this->Query('SELECT FOUND_ROWS() as TOTAL;')));
			$result['PAGER']['CURRENT'] = $arParams['PAGE'];
			$result['PAGER']['LIMIT'] = $arParams['LIMIT'];
			$result['PAGER']['SELECTED'] = count($result['ELEMENTS']);
		}

		$result['ELEMENTS'] = $this->HookExternalFields($arParams['FIELDS'], $result['ELEMENTS']);

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
	public function GetList($arParams = []) {
		$this->_controlParams($arParams, [
			'SORT',
			'FILTER',
			'CONDITIONS',
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
	private function _controlParams($params = [], $keys = []) {
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
	 * @param mixed $filter идентификатор | список идентификаторов | ассоциатив фильтров
	 * @param array|string $fields выбираемые поля
	 * @param array $sort сортировка
	 * @param bool $escape автоматически обрабатывать поля с выбором формата text/html в HTML-безопасный вид? (по умолчанию TRUE)
	 * @return array|false ассоциативный массив, представляющий собой элемент
	 */
	public function Read($filter = [], $fields = ['*@'], $sort = [], $escape = false) {
		$arParams = [
			"FILTER" => $filter,
			"FIELDS" => $fields,
			"SORT"   => $sort,
			"LIMIT"  => 1,
			"ESCAPE" => $escape,
		];
		$data = $this->Search($arParams);
		$element = reset($data["ELEMENTS"]);
		return $element;
	}

	/**
	 * Проверяет присутствует ли элемент с указанным идентификатором в таблице
	 *
	 * @param mixed $filter идентификатор | список идентификаторов | ассоциатив фильтров
	 * @return boolean true - если присутствует, false - если не присутствует
	 */
	public function Present($filter) {
		return (bool)$this->Read($filter, $this->keys);
	}

	/**
	 * Выбирает список идентификаторов\значений указанной колонки.
	 *
	 * @param mixed $filter идентификатор | список идентификаторов | ассоциатив фильтров
	 * @param array $sort сортировка (не обязательно)
	 * @param string $field символьный код выбираемой колонки (не обязательно, по умолчанию - идентификатор)
	 * @return array массив идентификаторов элементов
	 */
	public function Select($filter = [], $sort = [], $field = null) {
		if (is_null($field)) {
			$field = $this->key();
		}
		$elements = $this->GetList([
			'FILTER' => $filter,
			'FIELDS' => [$field],
			'SORT'   => $sort,
		]);
		$rows = [];
		foreach ($elements as $key => $element) {
			$rows[$key] = $element[$field];
		}
		return $rows;
	}

	public function Pick($filter = [], $sort = [], $field = null) {
		$data = $this->Select($filter, $sort, $field);
		return reset($data);
	}

	public function Count($filter = []) {
		$data = $this->Select($filter);
		return count($data);
	}

	/**
	 * Анализирует значение на наличие информации.
	 * - 0 - информация присутствует
	 * - 0.0 - информация присутствует
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
	public function _hasInformation($value) {
		if ($value === 0 || $value === 0.0 || $value === '0' || $value === false) {
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
	public function ControlFields($fields = []) {
		return $fields;
	}

	/**
	 * Создает новую строку в таблице и возвращает ее идентификатор
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
			if ($field['NOT_NULL'] && !$field['AUTO_INCREMENT'] && !isset($field['DEFAULT'])) {
				if (!$this->_hasInformation($fields[$code])) {
					throw new Exception("Не указано обязательное поле '{$field['NAME']}' [{$code}]");
				}
			}
		}

		$rows = [];
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
	 *
	 * @param mixed $filter идентификатор | список идентификаторов | ассоциатив фильтров
	 * @param array $fields ассоциативный массив изменяемых полей
	 * @throws Exception Нет информации для обновления
	 * @throws Exception Поле ... не может быть пустым
	 */
	public function Update($filter = [], $fields = []) {

		$fields = $this->ControlFields($fields);

		if (empty($fields)) {
			throw new Exception("No data to update");
		}

		$this->SQL = "UPDATE {$this->code} SET ";

		$rows = [];
		foreach ($this->structure as $code => $field) {
			if (array_key_exists($code, $fields)) {
				$rows[] = $this->_prepareSet($code, $fields[$code]);
			}
		}
		if (empty($rows)) {
			throw new Exception("No rows to update");
		}
		$this->SQL .= implode(",\r\n", $rows);

		$answer = $this->PrepareWhereAndJoinByFilter($filter);
		$this->SQL .= "\r\n WHERE " . implode(' AND ', $answer['WHERE']);

		$this->Query($this->SQL);
	}

	/**
	 * Удаляет строки из таблицы
	 *
	 * @param mixed $filter идентификатор | список идентификаторов | ассоциатив фильтров
	 */
	public function Delete($filter = []) {
		$answer = $this->PrepareWhereAndJoinByFilter($filter);
		$this->SQL = "DELETE FROM `{$this->code}` WHERE " . implode(' AND ', $answer['WHERE']);
		$this->Query($this->SQL);
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
			if (array_key_exists($key, $arParams)) {
				$defParams[$key] = $arParams[$key];
			}
		}
		return $defParams;
	}

	/**
	 * Создает массивы для выборки и джоинов
	 *
	 * @param array $fields поля для выборки
	 * @param string $prefix префикс
	 * @return array массив из двух элементов:
	 * - SELECT - []
	 * - JOIN - []
	 * @throws Exception
	 */
	public function PrepareSelectAndJoinByFields($fields, $prefix = "") {
		$select = [];
		$join = [];
		foreach ($fields as $code => $content) {
			if (!is_array($content)) {
				$code = strtoupper($content);
				$subfields = null;
			} else {
				$code = strtoupper($code);
				$subfields = $content;
			}
			unset($content);

			if (empty($this->structure[$code])) {
				throw new Exception("Unknown field code: '{$code}' in table '{$this->code}'");
			}
			$Type = $this->types[$code];
			$result = $Type->PrepareSelectAndJoinByField($this->code, $prefix, $subfields);
			$select += (array)$result['SELECT'];
			$join += (array)$result['JOIN'];
		}
		return [
			'SELECT' => $select,
			'JOIN'   => $join,
		];
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
	 * @param mixed $filter ассоциатив фильтров | список идентификаторов | идентификатор
	 * @return array :
	 * - WHERE - массив строк, представляющих собой SQL-условия, которые следует объеденить операторами AND или OR
	 * - JOIN - ассоциативный массив уникальных SQL-строк, описывающий присоединяемые таблицы
	 * @throws Exception
	 */
	public function PrepareWhereAndJoinByFilter($filter) {
		if (!is_array($filter) and empty($filter)) {
			throw new ExceptionNotAllowed("Empty non-array filter, ID missed?");
		}
		if (is_array($filter) and empty($filter)) {
			return [
				'WHERE' => [],
				'JOIN'  => [],
			];
		}
		if (!is_array($filter)) {
			// filter is identifier
			$filter = [$this->key() => $filter];
		}
		// if array does not has string keys
		if (count(array_filter(array_keys($filter), 'is_string')) === 0) {
			// and if array does not has array values
			if (count(array_filter($filter, 'is_array')) === 0) {
				// filter is list of identifiers
				$filter = [$this->key() => $filter];
			}
		}

		$where = [];
		$join = [];

		foreach ($filter as $filter_key => $values) {
			if ($values === '') {
				continue;
			}

			// вложенные операторы AND и OR
			if ($filter_key === 'AND' || $filter_key === 'OR' || is_numeric($filter_key)) {
				if (!is_array($values)) {
					throw new Exception("При использовании в фильтре вложенных операторов AND и OR значение должно быть массивом условий");
				}
				if (!is_numeric($filter_key)) {
					$logic = $filter_key;
				} else {
					$logic = $values['LOGIC'];
					if (!in_array($logic, ['AND', 'OR'])) {
						throw new Exception("При использовании в фильтре цифрового ключа значение-массив должно содержать ключ LOGIC = AND|OR");
					}
					unset($values['LOGIC']);
				}
				$answer = $this->PrepareWhereAndJoinByFilter($values);
				if (!empty($answer['WHERE'])) {
					$where[] = '(' . implode(" {$logic} ", $answer['WHERE']) . ')';
				}
				$join += $answer['JOIN'];
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
			$join += $result['JOIN'];

			$values = is_array($values) ? $values : [$values];
			$values_have_null = false;

			if (count($values) === 0) {
				$values_have_null = true;
			}

			foreach ($values as $key => $value) {
				if (is_null($value)) {
					$values_have_null = true;
					unset($values[$key]);
				} else {
					$values[$key] = $object->_formatFieldValue($code, $values[$key]);
				}
			}

			$conditions = [];
			if ($values_have_null) {
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
			if (count($values) === 1) {
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
			}
			if (count($values) > 1) {
				if (!empty($values)) {
					$conditions[] = ' IN ("' . implode('", "', $values) . '")';
				}
			}

			// TODO extract this block to TypeDateTime
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
		return [
			'WHERE' => $where,
			'JOIN'  => $join,
		];
	}

	/**
	 * Обрабатывает путь к полю.
	 * Вычисляет объект для обработки поля, псевдоним таблицы и код поля.
	 * Возвращает структуру с ключами:
	 * - OBJECT - объект-наследник SCRUD для обработки поля
	 * - TABLE - псевдоним для таблицы (alias)
	 * - CODE - код поля
	 * - JOIN - ассоциативный массив уникальных SQL-строк, описывающий присоединяемые таблицы
	 *
	 * @param string $field_path мнемонический путь к полю, например: 'EXTERNAL_FIELD.EXTERNAL_FIELD.FIELD'
	 * @return array
	 * @throws Exception Unknown external field code
	 * @throws Exception Field is not external
	 */
	protected function _treatFieldPath($field_path) {
		$path = explode(".", $field_path); // EXTERNAL_FIELD.EXTERNAL_FIELD.FIELD
		$code = array_pop($path); // FIELD
		if (empty($path)) {
			return [
				'OBJECT' => $this,
				'TABLE'  => $this->code,
				'CODE'   => $code,
				'JOIN'   => [],
			];
		}
		// if (!empty($path)):
		/** @var self $Object */
		/** @var Type $Type */
		$Object = $this;
		$prefix = '';
		$join = [];

		foreach ($path as $external) {
			$structure = &$Object->structure;
			$types = &$Object->types;
			$info = $structure[$external];
			$Type = $types[$external];

			if (empty($info)) {
				throw new Exception("Unknown external field code: '{$external}'");
			}
			if (empty($info['LINK'])) {
				throw new Exception("Field is not external: '{$external}'");
			}

			$join += $Type->GenerateJoinStatements($Object, $prefix);

			// for next iteration
			$prefix .= $external . "__";
			$Object = $info['LINK']::I();
		}
		return [
			'OBJECT' => $Object,
			'TABLE'  => $prefix . $Object->code,
			'CODE'   => $code,
			'JOIN'   => $join,
		];
	}

	/**
	 * Подготавливает часть SQL запроса ORDER BY
	 *
	 * @param array $array Массив фильтра SORT
	 * @return array Массив с ключами ORDER BY
	 */
	protected function _prepareOrder($array) {
		$order = [];
		foreach ($array as $field_path => $sort) {
			if ('RAND()' === $field_path) {
				$order[] = "RAND() {$sort}";
				continue;
			}
			$result = $this->_treatFieldPath($field_path);
			$order[] = "{$result['TABLE']}.`{$result['CODE']}` {$sort}";
		}
		return $order;
	}

	/**
	 * Подготавливает часть SQL запроса GROUP BY
	 *
	 * @param array $array Массив фильтра GROUP
	 * @return array Массив с ключами GROUP BY
	 */
	protected function _prepareGroup($array) {
		$answer = [];
		foreach ($array as $field_path) {
			$result = $this->_treatFieldPath($field_path);
			$answer[] = "{$result['TABLE']}.`{$result['CODE']}`";
		}
		return $answer;
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
		if (!isset($this->structure[$code])) {
			throw new Exception("Неизвестный код поля: '{$code}'");
		}
		$info = $this->structure[$code];

		if (!$this->_hasInformation($value)) {
			return null;
		}

		$value = $this->types[$code]->FormatInputValue($value);

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
	 * Возвращает экземпляр класса SCRUD, на который ссылается поле
	 *
	 * @param array $info массив, описывающий поле (с ключем LINK)
	 * @return SCRUD экземпляр
	 * @throws ExceptionNotAllowed
	 */
	private function GetLink(array $info) {
		if (!class_exists($info['LINK'])) {
			throw new ExceptionNotAllowed("You must set class name to LINK info of field '{$info['NAME']}'");
		}
		$parents = class_parents($info['LINK']);
		if (!in_array('System\SCRUD', $parents)) {
			throw new ExceptionNotAllowed("You must set class (child of SCRUD) name to LINK info of field '{$info['NAME']}'");
		}
		/** @var SCRUD $Link */
		$Link = $info['LINK']::I();
		return $Link;
	}


	public function ExplainFields($fields) {
		$output = [];
		foreach ($fields as $key => $value) {
			if (is_numeric($key) and is_array($value)) {
				throw new ExceptionNotAllowed("{$this->name}->ExplainFields: Numeric key '{$key}' with array value");
			}
			$o_key = is_numeric($key) ? $value : $key;

			if (is_array($value)) {
				if (!isset($this->structure[$key])) {
					throw new Exception("{$this->name}->ExplainFields: Unknown field with code '{$key}'");
				}
				$output[$o_key] = $this->GetLink($this->structure[$key])->ExplainFields($value);
				continue;
			}

			// if (!is_array($value)):
			$first_symbol = substr($value, 0, 1);
			if (!in_array($first_symbol, ['*', '@'])) {
				$output[$o_key] = $value;
				continue;
			}

			// if (in_array($first_symbol, ['*', '@'])):
			$last_symbols = substr($value, 1);
			foreach ($this->structure as $code => $info) {
				if ($first_symbol === '@' and !$info['VITAL']) {
					continue;
				}
				if (!isset($info['LINK'])) {
					$output[$code] = $code;
					continue;
				}
				if (empty($last_symbols)) {
					$output[$code] = $code;
					continue;
				}

				$output[$code] = $this->GetLink($info)->ExplainFields([$last_symbols]);
				continue;
			}
		}
		return $output;
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
		$element = [];
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
	 * Format output element values from database to user.
	 * No escape.
	 *
	 * @param array $element output element
	 * @return array output element with formatted values
	 * @throws Exception Unknown field code
	 */
	public function FormatOutputValues($element) {
		if (!is_array($element)) {
			return $element;
		}
		foreach ($element as $code => $value) {
			$info = $this->structure[$code];
			if (empty($info)) {
				throw new Exception("Unknown field code '{$code}'");
			}

			$element = $this->types[$code]->FormatOutputValue($element);
		}
		return $element;
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
	 * @return array|int результат выполнения
	 * @throws ExceptionSQL
	 */
	public function Query($SQL, $key = null) {
		try {
			return $this->DB->Query($SQL, $key);
		} catch (ExceptionSQL $ExceptionSQL) {
			$need_sync = substr($ExceptionSQL->getMessage(), 0, 14) === 'Unknown column';
			$need_sync |= substr($ExceptionSQL->getMessage(), -13) === 'doesn\'t exist';
			if ($need_sync) {
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
	public function ExtractStructure($codes = []) {
		$structure = [];
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
		return "/admin/{$name}.php";
	}

	public function GetElementTitle($element = []) {
		return $element['TITLE'] ?: $element['ID'] ?: '?';
	}

	/**
	 * Для всех полей в выборке запускает метод HookExternalField.
	 * Это позволяет типу поля подцеплять внешние данные к элементам выборки (если это требуется).
	 * Например тип TypeInner подцепляет все внешние элементы, ссылающиеся на выбранные элементы.
	 *
	 * @param array $fields древовидный массив, описывающий выбираемые поля
	 * @param array $elements элементы выборки
	 * @return array элементы выборки, дополненные внешними данными
	 */
	private function HookExternalFields($fields, $elements) {
		foreach ($fields as $code => $content) {
			if (!is_array($content)) {
				$code = strtoupper($content);
				$subfields = null;
			} else {
				$code = strtoupper($code);
				$subfields = $content;
			}
			$elements = $this->types[$code]->HookExternalField($elements, $subfields);
		}
		return $elements;
	}

}
