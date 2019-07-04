<?php

namespace System;

class User extends Instanceable {

	/** @var bool user is in current session */
	public $CURRENT = false;
	/** @var null|int user identifier */
	public $ID = null;
	/** @var array user fields (associative) */
	public $FIELDS = [];
	/** @var array user groups (list) */
	public $GROUPS = [];

	public function __construct($ID = null) {
		$this->CURRENT = ($ID === null);
		$this->Load($ID ?: $_SESSION['USER']['ID'] ?: null);
	}

	public function Load($ID) {
		$this->ID = $ID;
		$this->FIELDS = [];
		$this->GROUPS = [];
		if (!empty($this->ID)) {
			$this->FIELDS = Users::I()->Read($this->ID);
			if (empty($this->FIELDS)) {
				throw new ExceptionElementNotFound();
			}
			$group_ids = Users2Groups::I()->GetColumn(['USER' => $this->ID], 'GROUP');
			$this->GROUPS = Groups::I()->GetColumn(['ID' => $group_ids], 'CODE');
			$_SESSION['USER']['LANG'] = $this->FIELDS['LANG'];
		}
	}

	public function Authorization($login, $password) {
		if (empty($login)) {
			throw new Exception(T([
				'en' => 'Login must be specified',
				'ru' => 'Не указан логин',
			]));
		}
		$user = Users::I()->Read(['LOGIN' => $login], ['ID', 'SALT', 'LOGIN', 'PASSWORD']);
		if (empty($user)) {
			Log::I()->Create([
				'TYPE'    => 'USER_AUTH_NOT_FOUND',
				'MESSAGE' => "User '{$login}' does not exist",
				'DATA'    => ['LOGIN' => $login],
			]);
			throw new Exception(T([
				'en' => "User '{$login}' does not exist",
				'ru' => "Пользователь '{$login}' не существует",
			]));
		}
		if ($user['PASSWORD'] <> sha1($user['SALT'] . ':' . $password)) {
			Log::I()->Create([
				'TYPE'    => 'USER_AUTH_WRONG_PASSWORD',
				'MESSAGE' => "An incorrect password was entered, login: '{$login}'",
				'DATA'    => ['LOGIN' => $login],
				'USER'    => $user['ID'],
			]);
			throw new Exception(T([
				'en' => 'An incorrect password was entered',
				'ru' => 'Введен некорректный пароль',
			]));
		}
		$this->Login($user['ID']);
	}

	public function Login($ID) {
		$ID = (int)$ID;
		if (!Users::I()->Present($ID)) {
			throw new Exception(T([
				'en' => "User #{$ID} not found",
				'ru' => "Пользователь №{$ID} не найден",
			]));
		}

		Users::I()->Update($ID, ['LAST_AUTH' => time()]);
		Log::I()->Create([
			'USER'    => $ID,
			'TYPE'    => 'USER_AUTH_SUCCESS',
			'MESSAGE' => 'Successful authorization',
		]);
		$_SESSION['USER']['ID'] = $ID;
		$this->Load($ID);
	}

	public function Logout() {
		unset($_SESSION['USER']);
		$this->Load(null);
	}

	public function IsAuthorized() {
		return !empty($this->ID);
	}

	/**
	 * Check group affiliation
	 *
	 * @param string $group code of the group
	 * @return bool
	 */
	public function InGroup($group) {
		return in_array($group, $this->GROUPS);
	}
}