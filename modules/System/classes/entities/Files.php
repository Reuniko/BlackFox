<?php

namespace System;

class Files extends SCRUD {

	public function Init() {
		$this->name = 'Файлы';
		$this->groups = [
			'SYSTEM' => 'Системные поля',
			'FILE'   => 'Файл',
		];
		$this->structure += [
			'ID'          => self::ID + ['GROUP' => 'SYSTEM'],
			'CREATE_DATE' => [
				'TYPE'     => 'DATETIME',
				'NAME'     => 'Дата создания',
				'GROUP'    => 'SYSTEM',
				'DISABLED' => true,
			],
			'CREATE_BY'   => [
				'TYPE'     => 'OUTER',
				'NAME'     => 'Кем создан',
				'LINK'     => 'System\Users',
				'GROUP'    => 'SYSTEM',
				'DISABLED' => true,
			],
			'NAME'        => [
				'TYPE'  => 'STRING',
				'NAME'  => 'Имя файла',
				'VITAL' => true,
				'SHOW'  => true,
				'GROUP' => 'FILE',
			],
			'SIZE'        => [
				'TYPE'  => 'NUMBER',
				'NAME'  => 'Размер файла',
				'VITAL' => true,
				'GROUP' => 'FILE',
			],
			'TYPE'        => [
				'TYPE'  => 'STRING',
				'NAME'  => 'Тип контента',
				'VITAL' => true,
				'GROUP' => 'FILE',
			],
			'SRC'         => [
				'TYPE'  => 'STRING',
				'NAME'  => 'Путь к файлу',
				'VITAL' => true,
				'GROUP' => 'FILE',
			],
		];
	}

	public function GetNewSrc($full_name) {
		$extension = end(explode('.', $full_name));
		if (empty($extension)) {
			throw new Exception("File must have extension");
		}

		$dir = '';
		$src = '';
		while (true) {
			$name = sha1(time() . $full_name) . '.' . $extension;
			$dir = '/upload/' . substr($name, 0, 3);
			$src = $dir . '/' . $name;
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $src)) {
				continue;
			}
			break;
		}
		@mkdir($_SERVER['DOCUMENT_ROOT'] . $dir);
		return $src;
	}

	public function Create($fields = []) {
		if (isset($fields['tmp_name'])) {

			if ($fields['error'] !== 0) {
				return null;
			}

			$src = $this->GetNewSrc($fields['name']);

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

	public function CreateFromContent($name, $content) {
		$src = $this->GetNewSrc($name);
		$path = $_SERVER['DOCUMENT_ROOT'] . $src;
		file_put_contents($path, $content);
		return $this->Create([
			'CREATE_DATE' => time(),
			'NAME'        => $name,
			'SIZE'        => filesize($path),
			'TYPE'        => filetype($path),
			'SRC'         => $src,
		]);
	}


	public function GetElementTitle($element = []) {
		return $element['NAME'];
	}

	public function GetPrintableFileSize($size) {
		if ($size > 1024 * 1024) {
			return ceil($size / 1024 * 1024) . ' мб.';
		}
		if ($size > 1024) {
			return ceil($size / 1024) . ' кб.';
		}
		return $size . ' б.';
	}
}