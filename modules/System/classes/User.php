<?php
namespace System;

class User extends Instanceable {

	public function Authorization($login, $password) {
		if (empty($login)) {
			throw new Exception("Не указан логин");
		}
		$user = Users::I()->Read(['LOGIN' => $login], ['ID', 'SALT', 'LOGIN', 'PASSWORD']);
		if (empty($user)) {
			throw new Exception("Пользователь '{$login}' не существует");
		}
		if ($user['PASSWORD'] <> sha1($user['SALT'] . ':' . $password)) {
			throw new Exception("Введен некорректный пароль");
		}
		$this->Login($user['ID']);
	}

	public function Login($ID) {
		if (!Users::I()->Present($ID)) {
			throw new Exception("User #{$ID} not found");
		}
		Users::I()->Update($ID, ['LAST_AUTH' => time()]);
		$_SESSION['USER'] = Users::I()->Read($ID);
		$_SESSION['USER']['GROUPS'] = $this->GetGroups($ID);
	}

	public function Logout() {
		unset($_SESSION['USER']);
	}

	public function IsAuthorized() {
		return isset($_SESSION['USER']);
	}

	/**
	 * Get user groups
	 *
	 * @return array list of group codes
	 */
	public function GetGroups() {
		$ID = $_SESSION['USER']['ID'];
		$group_ids = Users2Groups::I()->Select(['USER' => $ID], [], 'GROUP');
		$groups = Groups::I()->Select(['ID' => $group_ids], [], 'CODE');
		return $groups;
	}

	/**
	 * Check group affiliation
	 *
	 * @param string $group code of the group
	 * @return bool
	 */
	public function InGroup($group) {
		return in_array($group, $this->GetGroups());
	}
}