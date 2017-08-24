<?php
namespace System;
class User extends SCRUD {

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
			],
			'PASSWORD'    => [
				'TYPE'        => 'STRING',
				'NAME'        => 'Пароль',
				'DESCRIPTION' => 'В базе хранится sha1 хеш пароля',
				'NOT_NULL'    => true,
			],
			'SALT'        => [
				'TYPE'     => 'STRING',
				'NAME'     => 'Соль',
				'NOT_NULL' => true,
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
			'AVATAR'      => [
				'TYPE' => 'FILE',
				'NAME' => 'Аватар',
				'LINK' => '\\System\\File',
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

	public function Authorization($login, $password) {
		if (empty($login)) {
			throw new Exception("Не указан логин");
		}
		$user = $this->Read(['LOGIN' => $login], ['ID', 'SALT', 'LOGIN', 'PASSWORD']);
		if (empty($user)) {
			throw new Exception("Пользователь '{$login}' не существует");
		}
		if ($user['PASSWORD'] <> sha1($user['SALT'] . ':' . $password)) {
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
		unset($_SESSION['USER']);
	}

	public function IsAuthorized() {
		return isset($_SESSION['USER']);
	}

}