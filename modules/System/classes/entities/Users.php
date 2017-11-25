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
				'JOIN'     => true,
				'SHOW'     => true,
			],
			'PASSWORD'    => [
				'TYPE'        => 'STRING',
				'NAME'        => 'Пароль',
				'DESCRIPTION' => 'В базе хранится sha1 хеш пароля',
				'NOT_NULL'    => true,
				'DISABLED'    => true,
			],
			'SALT'        => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Соль',
				'NOT_NULL' => true,
				'DISABLED' => true,
			],
			'LAST_AUTH'   => [
				'TYPE' => 'DATETIME',
				'NAME' => 'Последнее время авторизации',
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
			'PHONE'       => [
				'TYPE' => 'STRING',
				'NAME' => 'Телефон',
			],
			'AVATAR'      => [
				'TYPE' => 'FILE',
				'NAME' => 'Аватар',
				'LINK' => '\\System\\Files',
			],
			'BIRTH_DAY'   => [
				'TYPE' => 'DATE',
				'NAME' => 'День рождения',
			],
			'ABOUT'       => [
				'TYPE' => 'TEXT',
				'NAME' => 'О себе',
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
			throw new Exception("Login must be specified");
		}

		// prevent doubles for LOGIN
		if (!empty($this->Read(['LOGIN' => $fields['LOGIN']], ['ID']))) {
			throw new Exception("User with LOGIN '{$fields['LOGIN']}' already exist");
		}

		// auto hash password
		unset($fields['SALT']);
		if (!empty($fields['PASSWORD'])) {
			$fields['SALT'] = bin2hex(random_bytes(32));
			$fields['PASSWORD'] = sha1($fields['SALT'] . ':' . $fields['PASSWORD']);
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
			throw new Exception("Password must be specified");
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
		if (!empty($fields['PASSWORD'])) {
			$ids = is_array($ids) ? $ids : [$ids];
			foreach ($ids as $ID) {
				$this->SetPassword($ID, $fields['PASSWORD']);
			}
			unset($fields['PASSWORD']);
		}
		return parent::Update($ids, $fields);
	}

	public function AddGroup($ID, $group) {
		$ID = (int)$ID;
		if (empty($ID)) {
			throw new Exception("ID required");
		}
		if (is_string($group)) {
			$group_id = Groups::I()->Pick(['CODE' => $group]);
		} else {
			$group_id = (int)$group;
		}
		if (empty($group_id)) {
			throw new Exception("Group ID required");
		}
		Users2Groups::I()->Create([
			'USER'  => $ID,
			'GROUP' => $group_id,
		]);
	}

}