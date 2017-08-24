<?php
namespace System;
class Group extends SCRUD {

	public function Init() {
		parent::Init();
		$this->name = 'Группы пользователей';
		$this->composition['SYSTEM']['FIELDS'] += [
			'CODE'        => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Символьный код',
				'NOT_NULL' => true,
				'INDEX'    => true,
			],
			'NAME'        => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Имя',
				'NOT_NULL' => true,
				'INDEX'    => true,
				'JOIN'     => true,
			],
			'DESCRIPTION' => [
				'TYPE' => 'TEXT',
				'NAME' => 'Имя',
			],
		];
	}

}
