<?php

namespace System;

class Files extends SCRUD {

	public function Init() {
		$this->name = 'Файлы';
		$this->groups = ['SYSTEM' => 'Файл'];
		$this->structure += [
			'ID'          => self::ID,
			'CREATE_DATE' => [
				'TYPE'  => 'DATETIME',
				'NAME'  => 'Дата создания',
				'GROUP' => 'SYSTEM',
			],
			'CREATE_BY'   => [
				'TYPE'  => 'LINK',
				'NAME'  => 'Кем создан',
				'LINK'  => 'System\Users',
				'GROUP' => 'SYSTEM',
			],
			'NAME'        => [
				'TYPE'  => 'STRING',
				'NAME'  => 'Имя файла',
				'VITAL' => true,
				'SHOW'  => true,
				'GROUP' => 'SYSTEM',
			],
			'SIZE'        => [
				'TYPE'  => 'NUMBER',
				'NAME'  => 'Размер файла',
				'VITAL' => true,
				'GROUP' => 'SYSTEM',
			],
			'TYPE'        => [
				'TYPE'  => 'STRING',
				'NAME'  => 'Тип контента',
				'VITAL' => true,
				'GROUP' => 'SYSTEM',
			],
			'SRC'         => [
				'TYPE'  => 'STRING',
				'NAME'  => 'Путь к файлу',
				'VITAL' => true,
				'GROUP' => 'SYSTEM',
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