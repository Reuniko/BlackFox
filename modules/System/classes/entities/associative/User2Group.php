<?php
namespace System;
class User2Group extends SCRUD {
	public function Init() {
		$this->name = 'Пользователи в группах';
		$this->composition = [
			'SYSTEM' => [
				'NAME'   => 'Системные поля',
				'FIELDS' => [
					'ID'    => self::ID,
					'USER'  => [
						'TYPE' => 'LINK',
						'LINK' => '\\System\\User',
						'NAME' => 'Пользователь',
					],
					'GROUP' => [
						'TYPE' => 'LINK',
						'LINK' => '\\System\\Group',
						'NAME' => 'Группа',
					],
				],
			],
		];
	}

}