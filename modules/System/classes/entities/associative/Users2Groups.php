<?php

namespace System;
class Users2Groups extends SCRUD {
	public function Init() {
		$this->name = 'Пользователи в группах';
		$this->structure = [
			'ID'    => self::ID,
			'USER'  => [
				'TYPE' => 'LINK',
				'LINK' => '\\System\\Users',
				'NAME' => 'Пользователь',
			],
			'GROUP' => [
				'TYPE' => 'LINK',
				'LINK' => '\\System\\Groups',
				'NAME' => 'Группа',
			],
		];
	}

}