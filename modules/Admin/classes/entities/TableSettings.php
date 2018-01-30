<?php

namespace Admin;

class TableSettings extends \System\SCRUD {

	public function Init() {
		$this->name = 'Персональные настройки таблиц';
		$this->structure = [
			'ID'      => self::ID,
			'USER'    => [
				'NAME' => 'Пользователь',
				'TYPE' => 'LINK',
				'LINK' => 'System\Users',
			],
			'ENTITY'  => [
				'NAME'     => 'Сущность',
				'TYPE'     => 'STRING',
				'NOT_NULL' => true,
			],
			'FILTERS' => [
				'NAME' => 'Набор фильтров',
				'TYPE' => 'LIST',
			],
			'FIELDS'  => [
				'NAME' => 'Набор полей',
				'TYPE' => 'LIST',
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
