<?php
namespace System;

class Utility extends Instanceable {
	/**
	 * Форматирует дату в любом формате (стока|таймштамп)
	 * Возвращает пустую строку если дата пуста
	 *
	 * @param string|int $date дата в любом формате
	 * @param string $format формат (по умолчанию 'd.m.Y H:i')
	 * @return string отформатированная дата
	 */
	public static function Date($date, $format = 'd.m.Y H:i') {
		if (empty($date)) {
			return '';
		}
		if (!is_numeric($date)) {
			$date = strtotime($date);
		}
		return date($format, $date);
	}

	/**
	 * Форматирует имя пользователя
	 *
	 * @param array $user массив данных пользователя (FIRST_NAME, LAST_NAME, LOGIN, ID)
	 * @return string
	 */
	public static function Name($user = []) {
		$name = trim("{$user['FIRST_NAME']} {$user['LAST_NAME']}");
		if (empty($name)) {
			$name = $user['LOGIN'];
		}
		if (empty($name)) {
			$name = "[ID:{$user['ID']}]";
		}
		return $name;
	}
}