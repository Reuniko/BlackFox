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
		if (isset($fields['tmp_name'])) {

			if ($fields['error'] !== 0) {
				return null;
			}

			$file_ext = end(explode('.', $fields['name']));
			if (empty($file_ext)) {
				throw new Exception("File must have extension");
			}

			$dir = '';
			$src = '';
			while (true) {
				$file_name = sha1(time() . $fields['name']) . '.' . $file_ext;
				$dir = '/upload/' . substr($file_name, 0, 3);
				$src = $dir . '/' . $file_name;
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $src)) {
					continue;
				}
				break;
			}

			mkdir($_SERVER['DOCUMENT_ROOT'] . $dir);
			move_uploaded_file($fields['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $src);
			$file = [
				'CREATE_DATE' => time(),
				'CREATE_BY'   => User::I()->ID,
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