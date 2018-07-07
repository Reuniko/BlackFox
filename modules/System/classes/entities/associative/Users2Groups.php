<?php

namespace System;
class Users2Groups extends SCRUD {
	public function Init() {
		$this->name = 'Пользователи в группах';
		$this->structure = [
			'ID'    => self::ID,
			'USER'  => [
				'TYPE'  => 'OUTER',
				'LINK'  => 'System\Users',
				'NAME'  => 'Пользователь',
				'VITAL' => true,
			],
			'GROUP' => [
				'TYPE'  => 'OUTER',
				'LINK'  => 'System\Groups',
				'NAME'  => 'Группа',
				'VITAL' => true,
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