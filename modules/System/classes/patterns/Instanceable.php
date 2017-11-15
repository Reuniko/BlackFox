<?php
namespace System;

/**
 * Class Instanceable
 * Класс является прямым синонимом синглотну.
 * В отличие от синглтона этот паттерн не запрещает создание новых объектов того же класса.
 * В большинстве случаев в проекте требуется один и тот же неизменяемый глобальный объект.
 * Доступ к этому объекту осуществляется через <название класса>::Instance().
 * В других случаях при необходимости остается возможность создания личных локальных изменяемых объектов (new ...).
 * Рекомендуется при проектировании класса реализовать неизменяемость, анализируя флаг $this->instanced.
 * @package System
 */
abstract class Instanceable {

	/** @var array массив инстациированных классов */
	private static $instance = [];
	/** @var bool если класс был инстациирован - по возможности требуется запретить изменение его внутреннего состояния */
	protected $instanced = false;

	/**
	 * Возвращает инстациируемый объект:
	 * - если объект уже был создан - возвращает его
	 * - если объект еще не был создан - создает его и возвращает
	 *
	 * @param mixed $params параметры конструктора класса
	 * @return static инстациируемый объект
	 * @throws Exception Unsupported param type
	 */
	public static function Instance($params = null) {

		$class = get_called_class();

		global $CONFIG;
		if ($CONFIG['redirects'][$class]) {
			return $CONFIG['redirects'][$class]::Instance($params);
		}

		$hash = null;
		if (empty($params)) {
			$hash = 'DEFAULT';
		} elseif (is_string($params) or is_numeric($params)) {
			$hash = (string)$params;
		} elseif (is_array($params)) {
			$hash = md5(serialize($params));
		} else {
			throw new Exception("Unsupported param type");
		}

		if (is_null(self::$instance[$class][$hash])) {
			self::$instance[$class][$hash] = new $class($params);
			self::$instance[$class][$hash]->instanced = true;
		}
		return self::$instance[$class][$hash];
	}

	/**
	 * Возвращает инстациируемый объект:
	 * - если объект уже был создан - возвращает его
	 * - если объект еще не был создан - создает его и возвращает
	 *
	 * @param mixed $params параметры конструктора класса
	 * @return static инстациируемый объект
	 */
	public static function I($params = null) {
		return self::Instance($params);
	}

	public static function InstanceDefault($params = null) {
		$Object = self::Instance($params);
		$class = get_class($Object);
		self::$instance[$class]['DEFAULT'] = $Object;
		return $Object;
	}
}