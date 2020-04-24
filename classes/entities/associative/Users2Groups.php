<?php

namespace BlackFox;
class Users2Groups extends SCRUD {
	public function Init() {
		$this->name = T([
		    'en' => 'Users in groups',
		    'ru' => 'Пользователи в группах',
		]);
		$this->structure = [
			'ID'    => self::ID,
			'USER'  => [
				'TYPE'    => 'OUTER',
				'LINK'    => 'Users',
				'NAME'    => T([
					'en' => 'User',
					'ru' => 'Пользователь',
				]),
				'VITAL'   => true,
				'FOREIGN' => 'CASCADE',
			],
			'GROUP' => [
				'TYPE'    => 'OUTER',
				'LINK'    => 'Groups',
				'NAME'    => T([
					'en' => 'Group',
					'ru' => 'Группа',
				]),
				'VITAL'   => true,
				'FOREIGN' => 'CASCADE',
			],
		];
	}

	public function Create($fields = []) {
		// TODO make double primary for SCRUD
		$element = $this->Read([
			'USER'  => $fields['USER'],
			'GROUP' => $fields['GROUP'],
		]);
		if (!empty($element)) {
			return $element['ID'];
		} else {
			return parent::Create($fields);
		}
	}

	public function GetElementTitle($element = []) {
		return $element['GROUP']['NAME'];
	}
}