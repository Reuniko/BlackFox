<?php

namespace Admin;

class TableSettings extends \System\SCRUD {

	public function Init() {
		$this->name = 'Персональные настройки таблиц';
		$this->structure = [
			'ID'      => self::ID,
			'USER'    => [
				'TYPE' => 'OUTER',
				'NAME' => 'Пользователь',
				'LINK' => 'System\Users',
			],
			'ENTITY'  => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Сущность',
				'NOT_NULL' => true,
			],
			'FILTERS' => [
				'TYPE' => 'LIST',
				'NAME' => 'Набор фильтров',
			],
			'FIELDS'  => [
				'TYPE' => 'LIST',
				'NAME' => 'Набор полей',
			],
		];
	}

	/**
	 * Сохраняет настройки отображения таблицы сущности
	 *
	 * @param int $user_id идентификатор пользователя
	 * @param string $entity_code символьный код сущности
	 * @param array $filters упорядоченный лист отображаемых фильтров
	 * @param array $fields упорядоченный лист отображаемых полей
	 */
	public function Save($user_id, $entity_code, $filters, $fields) {
		$element = $this->Read([
			'USER'   => $user_id,
			'ENTITY' => $entity_code,
		]);
		if (empty($element)) {
			$this->Create([
				'USER'    => $user_id,
				'ENTITY'  => $entity_code,
				'FILTERS' => $filters,
				'FIELDS'  => $fields,
			]);
		} else {
			$this->Update($element['ID'], [
				'FILTERS' => $filters,
				'FIELDS'  => $fields,
			]);
		}
	}

}
