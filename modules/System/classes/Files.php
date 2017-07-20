<?php

namespace System;

class Files extends SCRUD {
	public function Init() {
		parent::Init();
		$this->name = 'Файлы';
		$this->composition['SYSTEM']['FIELDS'] += [
			'CREATE_DATE' => [
				'TYPE' => 'DATETIME',
				'NAME' => 'Дата создания',
			],
			'CREATE_BY'   => [
				'TYPE' => 'LINK',
				'NAME' => 'Кем создан',
				'LINK' => '\\System\\Users',
			],
			'NAME'        => [
				'TYPE' => 'STRING',
				'NAME' => 'Имя файла',
				'JOIN' => true,
				'SHOW' => true,
			],
			'SIZE'        => [
				'TYPE' => 'NUMBER',
				'NAME' => 'Размер файла',
				'JOIN' => true,
			],
			'TYPE'        => [
				'TYPE' => 'STRING',
				'NAME' => 'Тип контента',
				'JOIN' => true,
			],
			'SRC'         => [
				'TYPE' => 'STRING',
				'NAME' => 'Путь к файлу',
				'JOIN' => true,
			],
		];
	}

	public function Create($fields = []) {
		Debug($_SERVER['DOCUMENT_ROOT'], 'DOCUMENT_ROOT', 'log');
		Debug($fields, 'Files Create $fields', 'log');
		if (isset($fields['tmp_name'])) {
			if ($fields['error'] !== 0) {
				return null;
			}
			$dir = '/upload/' . substr(sha1($fields['name'] . time()), 0, 3);
			mkdir($_SERVER['DOCUMENT_ROOT'] . $dir);
			$src = $dir . '/' . $fields['name'];
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $src)) {
				return null; // TODO: generate better name
			}
			move_uploaded_file($fields['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $src);
			$file = [
				'CREATE_DATE' => time(),
				'CREATE_BY'   => null, // TODO: paste user ID
				'NAME'        => $fields['name'],
				'SIZE'        => $fields['size'],
				'TYPE'        => $fields['type'],
				'SRC'         => $src,
			];
			return parent::Create($file);
		} else {
			return parent::Create($fields);
		}
	}

	public function Update($ids = array(), $fields = array()) {
		throw new ExceptionNotAllowed();
	}
}