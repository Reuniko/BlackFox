<?php

namespace BlackFox;

class Files extends SCRUD {

	public function Init() {
		$this->name = T([
			'en' => 'Files',
			'ru' => 'Файлы',
		]);
		$this->groups = [
			'SYSTEM' => T([
				'en' => 'System fields',
				'ru' => 'Системные поля',
			]),
			'FILE'   => T([
				'en' => 'File',
				'ru' => 'Файл',
			]),
		];
		$this->fields += [
			'ID'          => self::ID + ['GROUP' => 'SYSTEM'],
			'CREATE_DATE' => [
				'TYPE'     => 'DATETIME',
				'NAME'     => T([
					'en' => 'Create date',
					'ru' => 'Дата создания',
				]),
				'GROUP'    => 'SYSTEM',
				'DISABLED' => true,
			],
			'CREATE_BY'   => [
				'TYPE'     => 'OUTER',
				'NAME'     => T([
					'en' => 'Created by',
					'ru' => 'Кем создан',
				]),
				'LINK'     => 'Users',
				'GROUP'    => 'SYSTEM',
				'DISABLED' => true,
			],
			'NAME'        => [
				'TYPE'  => 'STRING',
				'NAME'  => T([
					'en' => 'Name',
					'ru' => 'Имя',
				]),
				'VITAL' => true,
				'GROUP' => 'FILE',
			],
			'SIZE'        => [
				'TYPE'  => 'INTEGER',
				'NAME'  => T([
					'en' => 'Size',
					'ru' => 'Размер',
				]),
				'VITAL' => true,
				'GROUP' => 'FILE',
			],
			'TYPE'        => [
				'TYPE'  => 'STRING',
				'NAME'  => T([
					'en' => 'Type',
					'ru' => 'Тип',
				]),
				'VITAL' => true,
				'GROUP' => 'FILE',
			],
			'SRC'         => [
				'TYPE'  => 'STRING',
				'NAME'  => T([
					'en' => 'Src',
					'ru' => 'Путь',
				]),
				'VITAL' => true,
				'GROUP' => 'FILE',
			],
		];
	}

	public function GetNewSrc($full_name) {
		$extension = end(explode('.', $full_name));
		if (empty($extension)) {
			throw new Exception(T([
				'en' => "File must have extension",
				'ru' => 'Файл должен обладать расширением',
			]));
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
		@mkdir($_SERVER['DOCUMENT_ROOT'] . '/upload');
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
			return parent::Create([
				'CREATE_DATE' => time(),
				'CREATE_BY'   => User::I()->ID,
				'NAME'        => $fields['name'],
				'SIZE'        => $fields['size'],
				'TYPE'        => $fields['type'],
				'SRC'         => $src,
			]);
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


	public function GetElementTitle(array $element = []) {
		return $element['NAME'];
	}

	public function GetPrintableFileSize($size) {
		if ($size > 1024 * 1024) {
			return ceil($size / 1024 * 1024) . T([
					'en' => ' mb.',
					'ru' => ' мб.',
				]);
		}
		if ($size > 1024) {
			return ceil($size / 1024) . T([
					'en' => ' kb.',
					'ru' => ' кб.',
				]);
		}
		return $size . T([
				'en' => ' b.',
				'ru' => ' б.',
			]);
	}
}