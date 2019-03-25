<?php

namespace System;
/**
 * Class Type
 * @package System
 *
 * Parent for all data types for using in database.
 */
abstract class Type implements \ArrayAccess {

	protected function Quote($id) {
		return Database::I()->Quote($id);
	}

	// -------------------------------------------------------------------------------------------------------------- //
	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->info[] = $value;
		} else {
			$this->info[$offset] = $value;
		}
	}

	public function offsetExists($offset) {
		return isset($this->info[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->info[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->info[$offset]) ? $this->info[$offset] : null;
	}
	// -------------------------------------------------------------------------------------------------------------- //

	/** @var string Mnemonic code of the type */
	public static $TYPE;

	/** @var array Settings of specific field */
	public $info;

	public function __construct(array &$info) {
		$this->info = &$info;
		$this->ProvideInfoIntegrity();
	}

	public function ProvideInfoIntegrity() {
	}

	/**
	 * Format input value from user to save into database.
	 * No escape required.
	 *
	 * @param mixed $value input value from user
	 * @internal array $info type info
	 * @return string input value for database
	 */
	public function FormatInputValue($value) {
		return $value;
	}

	/**
	 * Format the specific value of the output element from the database to the user.
	 * No escape required.
	 *
	 * The element is passed entirely to provide a possibility of adding specific keys.
	 *
	 * @param array $element output element
	 * @internal array $info type info
	 * @return array output element with formatted value|values
	 */
	public function FormatOutputValue($element) {
		return $element;
	}

	public function PrepareConditions($table, $operator, $values) {
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
				$values[$key] = $this->FormatInputValue($values[$key]);
				$values[$key] = Database::I()->Escape($values[$key]);
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
					$conditions['>>'] = '>\'' . $value . '\'';
					break;
				case '!':
				case '<>':
					$conditions['<>'] = '<>\'' . $value . '\'';
					break;
				case '<<':
					$conditions['<<'] = '<\'' . $value . '\'';
					break;
				case '<':
					$conditions['<'] = '<=\'' . $value . '\'';
					break;
				case '>':
					$conditions['>'] = '>=\'' . $value . '\'';
					break;
				case '~':
					$conditions['~'] = 'LIKE \'%' . $value . '%\'';
					break;
				default:
					$conditions['='] = '=\'' . $value . '\'';
					break;
			}
		}
		if (count($values) > 1) {
			if (!empty($values)) {
				$conditions['in'] = 'IN (\'' . implode('\', \'', $values) . '\')';
			}
		}

		foreach ($conditions as $key => $condition) {
			$conditions[$key] = $table . "." . $this->Quote($this->info['CODE']) . $condition;
		}

		return $conditions;
	}

	/**
	 * This method must generate and return array with keys:
	 * - SELECT - array of SQL parts for SELECT section
	 * - JOIN - array of SQL parts for JOIN section
	 *
	 * Генерирует и возвращает массивы строк, являющихся частями для SQL запроса.
	 * - SELECT - массив SQL частей для секции SELECT
	 * - JOIN - массив SQL частей для секции JOIN
	 *
	 * @param string $table code of targeted table
	 * @param string $prefix required prefix
	 * @param array|null $subfields may contain array of required subfields
	 * @internal array $info
	 * @return array
	 */
	public function PrepareSelectAndJoinByField($table, $prefix, $subfields) {
		$code = $this->info['CODE'];
		$select["{$prefix}{$code}"] = "{$prefix}{$table}" . "." . $this->Quote("{$code}") . " as " . $this->Quote("{$prefix}{$code}");
		return ['SELECT' => $select];
	}

	/**
	 * Подцепляет внешние данные к элементам выборки (если это требуется).
	 *
	 * @param array $elements
	 * @param array $subfields
	 * @internal array $info
	 * @return mixed
	 */
	public function HookExternalField($elements, $subfields) {
		return $elements;
	}

	/**
	 * Предоставляет типу возможность присоединить внешние таблицы при обращении к полю из фильтра
	 *
	 * @param SCRUD $Current объект текущей таблицы
	 * @param string $prefix префикс
	 * @internal array $info
	 * @return array :
	 * - JOIN - ['уникальный алиас присоединяемой таблицы' => 'SQL-строка, описывающая присоединяемую таблицу', ...]
	 * - GROUP - ['уникальный алиас присоединяемой таблицы' => 'SQL-строка, описывающая группировку', ...]
	 */
	public function GenerateJoinAndGroupStatements(SCRUD $Current, $prefix) {
		return [
			'JOIN'  => [],
			'GROUP' => [],
		];
	}

	/**
	 * Формирует отображение значения поля
	 *
	 * @param mixed $value
	 */
	public function PrintValue($value) {
		echo is_array($value) ? '<pre>' . print_r($value, true) . '</pre>' : $value;
	}

	/**
	 * Формирует отображение контрола для формы создания\редактирования элемента
	 *
	 * @param mixed $value
	 * @param string $name
	 * @param string $class
	 */
	public function PrintFormControl($value, $name, $class = 'form-control') {
		?>
		<? if (is_array($value)): ?>
			<textarea
				class="<?= $class ?>"
				id="<?= $name ?>"
				name="<?= $name ?>"
				rows="5"
				disabled="disabled"
			><?= print_r($value, true) ?></textarea>
		<? else: ?>
			<input
				type="text"
				class="<?= $class ?>"
				id="<?= $name ?>"
				name="<?= $name ?>"
				value="<?= $value ?>"
				disabled="disabled"
			/>
		<? endif; ?>
		<?
	}

	/**
	 * Формирует отображение контролов для формы фильтрации.
	 * Фильтр передается целиком для предоставления возможности формировать несколько фильтрующих контролов для одного поля.
	 *
	 * @param array $filter
	 * @param string $group
	 * @param string $class
	 */
	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
		$code = $this->info['CODE'];
		?>
		<input
			type="text"
			class="<?= $class ?>"
			id="<?= $group ?>[<?= $code ?>]"
			name="<?= $group ?>[<?= $code ?>]"
			value="<?= $filter[$code] ?>"
			disabled="disabled"
		/>
		<?
	}
}