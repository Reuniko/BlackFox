<?php

namespace Admin;

class TableSettings extends \System\SCRUD {

	public function Init() {
		$this->name = T([
			'en' => 'Personal table settings',
			'ru' => 'Персональные настройки таблиц',
		]);
		$this->structure = [
			'ID'      => self::ID,
			'USER'    => [
				'TYPE' => 'OUTER',
				'NAME' => T([
					'en' => 'User',
					'ru' => 'Пользователь',
				]),
				'LINK' => 'System\Users',
			],
			'ENTITY'  => [
				'TYPE'     => 'STRING',
				'NAME'     => T([
					'en' => 'Entity',
					'ru' => 'Сущность',
				]),
				'NOT_NULL' => true,
			],
			'FILTERS' => [
				'TYPE' => 'LIST',
				'NAME' => T([
					'en' => 'Filters set',
					'ru' => 'Набор фильтров',
				]),
			],
			'FIELDS'  => [
				'TYPE' => 'LIST',
				'NAME' => T([
					'en' => 'Fields set',
					'ru' => 'Набор полей',
				]),
			],
		];
	}

	/**
	 * Saves display settings of entity table
	 * Сохраняет настройки отображения таблицы сущности
	 *
	 * @param int $user_id user identifier
	 * @param string $entity_code symbolic code of entity
	 * @param array $filters sorted list of filters
	 * @param array $fields sorted list of fields
	 * @throws \System\Exception
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
