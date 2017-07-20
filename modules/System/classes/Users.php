<?php

namespace System;

class Users extends SCRUD {
	public function Init() {
		parent::Init();
		$this->name = 'Пользователи';
		$this->composition['SYSTEM']['FIELDS'] += [
			'LOGIN'       => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Логин',
				'NOT_NULL' => true,
				'INDEX'    => true,
				'UNIQUE'   => true,
			],
			'PASSWORD'    => [
				'TYPE'        => 'STRING',
				'NAME'        => 'Пароль',
				'DESCRIPTION' => 'В базе хранится sha1 хеш пароля',
				'NOT_NULL'    => true,
			],
			'FIRST_NAME'  => [
				'TYPE' => 'STRING',
				'NAME' => 'Имя',
			],
			'LAST_NAME'   => [
				'TYPE' => 'STRING',
				'NAME' => 'Фамилия',
			],
			'MIDDLE_NAME' => [
				'TYPE' => 'STRING',
				'NAME' => 'Отчество',
			],
			'EMAIL'       => [
				'TYPE' => 'STRING',
				'NAME' => 'E-mail',
			],
			'LAST_AUTH'   => [
				'TYPE' => 'DATETIME',
				'NAME' => 'Последнее время авторизации',
			],
		];
	}

	public function Create($fields) {
		if (empty($fields['PASSWORD'])) {
			throw new Exception("Не указан пароль");
		}
		if (!empty($this->Read(['LOGIN' => $fields['LOGIN']], ['ID']))) {
			throw new Exception("Пользователь с логином '{$fields['LOGIN']}' уже зарегистрирован в системе");
		}
		$fields['PASSWORD'] = sha1(strtolower($fields['LOGIN']) . ':' . $fields['PASSWORD']);
		return parent::Create($fields);
	}

	public function Update($ids = [], $fields = []) {
		if (!empty($fields['PASSWORD'])) {
			if (!empty($fields['LOGIN'])) {
				$fields['PASSWORD'] = sha1(strtolower($fields['LOGIN']) . ':' . $fields['PASSWORD']);
			} else {
				throw new Exception("Для корректной смены пароля требуется поле LOGIN");
			}
		}
		return parent::Update($ids, $fields);
	}

	public function Authorization($login, $password) {
		$user = $this->Read(['LOGIN' => $login], ['ID', 'LOGIN', 'PASSWORD']);
		if (empty($user)) {
			throw new Exception("Пользователь '{$login}' не существует");
		}
		$hash = sha1(strtolower($login) . ':' . $password);
		if ($user['PASSWORD'] <> $hash) {
			throw new Exception("Введен некорректный пароль");
		}
		$this->Login($user['ID']);
	}

	public function Login($ID) {
		$this->Update($ID, [
			'LAST_AUTH' => time(),
		]);
		$_SESSION['USER'] = $this->Read($ID);
	}

	public function Logout() {
		$_SESSION['USER'] = [];
	}
}