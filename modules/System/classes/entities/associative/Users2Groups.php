<?php

namespace System;
class Users2Groups extends SCRUD {
	public function Init() {
		$this->name = 'Пользователи в группах';
		$this->structure = [
			'ID'    => self::ID,
			'USER'  => [
				'TYPE' => 'OUTER',
				'LINK' => 'System\Users',
				'NAME' => 'Пользователь',
			],
			'GROUP' => [
				'TYPE' => 'OUTER',
				'LINK' => 'System\Groups',
				'NAME' => 'Группа',
			],
		];
	}

}