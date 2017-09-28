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
}