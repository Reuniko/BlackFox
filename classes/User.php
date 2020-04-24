<?php

namespace BlackFox;

class User extends Instanceable {

	/** @var null|int user identifier */
	public $ID = null;
	/** @var array user fields (associative) */
	public $FIELDS = [];
	/** @var array user groups (list) */
	public $GROUPS = [];

	public function __construct($ID = null) {
		$this->Load($ID ?: $_SESSION['USER']['ID'] ?: null);
	}

	/**
	 * Load the data about user
	 *
	 * @param null|int $ID null or user identifier
	 * @throws Exception User not found
	 */
	public function Load($ID) {
		$this->ID = $ID;
		$this->FIELDS = [];
		$this->GROUPS = [];

		if (is_null($this->ID)) return;

		$this->FIELDS = Users::I()->Read($this->ID);
		if (empty($this->FIELDS))
			throw new Exception(T([
				'en' => "User #{$ID} not found",
				'ru' => "Пользователь №{$ID} не найден",
			]));

		$group_ids = Users2Groups::I()->GetColumn(['USER' => $this->ID], 'GROUP');
		$this->GROUPS = Groups::I()->GetColumn(['ID' => $group_ids], 'CODE');

		$_SESSION['USER']['LANG'] = $this->FIELDS['LANG'];
	}

	/**
	 * Try to authorize the user with his requisites
	 *
	 * @param string $login
	 * @param string $password
	 * @throws Exception User not found
	 * @throws Exception An incorrect password was entered
	 */
	public function Authorization(string $login, string $password) {
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
				'MESSAGE' => "User '{$login}' not found",
				'DATA'    => ['LOGIN' => $login],
			]);
			throw new Exception(T([
				'en' => "User '{$login}' not found",
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

	/**
	 * Authorize the user by his identifier
	 *
	 * @param int $ID user identifier
	 * @throws Exception User not found
	 */
	public function Login(int $ID) {
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
	public function InGroup(string $group) {
		return in_array($group, $this->GROUPS);
	}
}