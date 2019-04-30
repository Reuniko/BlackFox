<?php

namespace System;

class Users extends SCRUD {

	public function Init() {
		$this->name = T([
			'en' => 'Users',
			'ru' => 'Пользователи',
		]);
		$this->groups = [
			'SYSTEM'  => T([
				'en' => 'System',
				'ru' => 'Система',
			]),
			'CONTENT' => T([
				'en' => 'Content',
				'ru' => 'Контент',
			]),
		];
		$this->structure += [
			'ID'              => self::ID + ['GROUP' => 'SYSTEM'],
			'LOGIN'           => [
				'TYPE'     => 'STRING',
				'GROUP'    => 'SYSTEM',
				'NAME'     => T([
					'en' => 'Login',
					'ru' => 'Логин',
				]),
				'NOT_NULL' => true,
				'INDEX'    => true,
				'UNIQUE'   => true,
				'VITAL'    => true,
			],
			'PASSWORD'        => [
				'TYPE'        => 'PASSWORD',
				'GROUP'       => 'SYSTEM',
				'NAME'        => T([
					'en' => 'Password',
					'ru' => 'Пароль',
				]),
				'DESCRIPTION' => T([
					'en' => 'Database contains sha1 hash of the password',
					'ru' => 'В базе хранится sha1 хеш пароля',
				]),
				'NOT_NULL'    => true,
			],
			'SALT'            => [
				'TYPE'     => 'STRING',
				'GROUP'    => 'SYSTEM',
				'NAME'     => T([
					'en' => 'Salt',
					'ru' => 'Соль',
				]),
				'NOT_NULL' => true,
			],
			'HASH'            => [
				'TYPE'        => 'STRING',
				'GROUP'       => 'SYSTEM',
				'NAME'        => T([
					'en' => 'Hash',
					'ru' => 'Хэш',
				]),
				'DESCRIPTION' => T([
					'en' => 'For password recovery',
					'ru' => 'Для восстановления пароля',
				]),
			],
			'LANG'            => [
				'TYPE'    => 'STRING',
				'GROUP'   => 'SYSTEM',
				'NAME'    => T([
					'en' => 'Language',
					'ru' => 'Язык',
				]),
				'DEFAULT' => 'en',
			],
			'LAST_AUTH'       => [
				'TYPE'     => 'DATETIME',
				'GROUP'    => 'SYSTEM',
				'NAME'     => T([
					'en' => 'Last authorisation moment',
					'ru' => 'Последнее время авторизации',
				]),
				'DISABLED' => true,
			],
			'REGISTER_MOMENT' => [
				'TYPE'     => 'DATETIME',
				'GROUP'    => 'SYSTEM',
				'NAME'     => T([
					'en' => 'Registration moment',
					'ru' => 'Время регистрации',
				]),
				'DISABLED' => true,
			],
			'FIRST_NAME'      => [
				'TYPE'  => 'STRING',
				'GROUP' => 'CONTENT',
				'NAME'  => T([
					'en' => 'First name',
					'ru' => 'Имя',
				]),
				'VITAL' => true,
			],
			'LAST_NAME'       => [
				'TYPE'  => 'STRING',
				'GROUP' => 'CONTENT',
				'NAME'  => T([
					'en' => 'Last name',
					'ru' => 'Фамилия',
				]),
				'VITAL' => true,
			],
			'MIDDLE_NAME'     => [
				'TYPE'  => 'STRING',
				'GROUP' => 'CONTENT',
				'NAME'  => T([
					'en' => 'Middle name',
					'ru' => 'Отчество',
				]),
			],
			'EMAIL'           => [
				'TYPE'  => 'STRING',
				'GROUP' => 'CONTENT',
				'NAME'  => T([
					'en' => 'E-mail',
					'ru' => 'E-mail',
				]),
				'VITAL' => true,
			],
			'PHONE'           => [
				'TYPE'  => 'STRING',
				'GROUP' => 'CONTENT',
				'NAME'  => T([
					'en' => 'Phone',
					'ru' => 'Телефон',
				]),
			],
			'AVATAR'          => [
				'TYPE'  => 'FILE',
				'GROUP' => 'CONTENT',
				'NAME'  => T([
					'en' => 'Avatar',
					'ru' => 'Аватар',
				]),
				'LINK'  => 'System\Files',
			],
			'BIRTH_DAY'       => [
				'TYPE'  => 'DATE',
				'GROUP' => 'CONTENT',
				'NAME'  => T([
					'en' => 'Birthday',
					'ru' => 'День рождения',
				]),
			],
			'ABOUT'           => [
				'TYPE'  => 'TEXT',
				'GROUP' => 'CONTENT',
				'NAME'  => T([
					'en' => 'About',
					'ru' => 'О себе',
				]),
			],
			'GROUPS'          => [
				'TYPE'  => 'INNER',
				'NAME'  => T([
					'en' => 'Groups',
					'ru' => 'Группы',
				]),
				'LINK'  => 'System\Users2Groups',
				'FIELD' => 'USER',
			],
		];
	}

	/**
	 * Creates a new user
	 *
	 * @param array $fields
	 * @return int created users identifier
	 * @throws Exception
	 */
	public function Create($fields) {

		if (empty($fields['LOGIN'])) {
			throw new Exception(T([
				'en' => 'Login must be specified',
				'ru' => 'Укажите логин',
			]));
		}

		// prevent doubles for LOGIN
		if (!empty($this->Read(['LOGIN' => $fields['LOGIN']], ['ID']))) {
			throw new Exception(T([
				'en' => "User with login '{$fields['LOGIN']}' already exist",
				'ru' => "Пользователь с логином '{$fields['LOGIN']}' уже существует",
			]));
		}

		// auto hash password
		unset($fields['SALT']);
		if (!empty($fields['PASSWORD'])) {
			$fields['SALT'] = bin2hex(random_bytes(32));
			$fields['PASSWORD'] = sha1($fields['SALT'] . ':' . $fields['PASSWORD']);
		}

		if (empty($fields['REGISTER_MOMENT'])) {
			$fields['REGISTER_MOMENT'] = time();
		}

		return parent::Create($fields);
	}

	/**
	 * Set password for specified user
	 *
	 * @param int $ID users identifier
	 * @param string $password a new password
	 * @throws Exception Password must be specified
	 */
	public function SetPassword($ID, $password) {
		if (empty($password)) {
			throw new Exception(T([
				'en' => 'Password must be specified',
				'ru' => 'Укажите пароль',
			]));
		}
		$salt = bin2hex(random_bytes(32));
		$password = sha1($salt . ':' . $password);
		parent::Update($ID, [
			'SALT'     => $salt,
			'PASSWORD' => $password,
		]);
	}

	/**
	 * Checks if users password match database hash
	 *
	 * @param int $ID user identifier
	 * @param string $password password to check
	 * @return bool
	 */
	public function CheckPassword($ID, $password) {
		$user = $this->Read($ID, ['PASSWORD', 'SALT']);
		return ($user['PASSWORD'] === sha1($user['SALT'] . ':' . $password));
	}


	public function Update($ids = [], $fields = []) {
		$ids = is_array($ids) ? $ids : [$ids];
		if (!empty($fields['PASSWORD'])) {
			foreach ($ids as $ID) {
				$this->SetPassword($ID, $fields['PASSWORD']);
			}
		}
		unset($fields['PASSWORD']);
		return parent::Update($ids, $fields);
	}

	public function AddGroup($ID, $group) {
		$ID = (int)$ID;
		if (empty($ID)) {
			throw new Exception(T([
				'en' => 'User ID required',
				'ru' => 'Требуется ID пользователя',
			]));
		}
		if (is_string($group)) {
			$group_id = Groups::I()->Pick(['CODE' => $group]);
		} else {
			$group_id = (int)$group;
		}
		if (empty($group_id)) {
			throw new Exception(T([
				'en' => 'Group ID/CODE required',
				'ru' => 'Требуется ID/CODE группы',
			]));
		}
		Users2Groups::I()->Create([
			'USER'  => $ID,
			'GROUP' => $group_id,
		]);
	}

	public function GetRecoveryString($ID) {
		$string = sha1(random_bytes(32));
		$hash = sha1($string);
		$this->Update($ID, ['HASH' => $hash]);
		return $string;
	}

	public function GetElementTitle($element = []) {
		return $element['LOGIN'];
	}

}