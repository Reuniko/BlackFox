<?php

namespace BlackFox;

/**
 * Class Instanceable
 * Класс является прямым синонимом синглотну.
 * В отличие от синглтона этот паттерн не запрещает создание новых объектов того же класса.
 * В большинстве случаев в проекте требуется один и тот же неизменяемый глобальный объект.
 * Доступ к этому объекту осуществляется через <название класса>::I() или <название класса>::I()
 * В других случаях при необходимости остается возможность создания личных локальных изменяемых объектов (new ...).
 * Рекомендуется при проектировании класса реализовать неизменяемость, анализируя флаг $this->instanced.
 *
 * Class Instanceable
 * Class is a direct synonym for singleton.
 * Unlike singleton, this pattern does not prohibit the creation of new objects of the same class.
 * In most cases, the project requires the same immutable global object.
 * Access to this object is through <class name>::I() or <class name>::I()
 * In other cases, if necessary, it remains possible to create personal local mutable objects (new ...).
 * It is recommended that when designing a class, implement immutability by analyzing the flag $this->instanced.
 *
 * @package BlackFox
 */
abstract class Instanceable {

	/** @var array array of instantiated classes */
	private static $instance = [];
	/** @var bool if the class has been instanced - in most cases it is required to prohibit a change in its internal state */
	protected $instanced = false;

	/**
	 * Returns the object being instantiated:
	 * - if the object has already been created - returns it
	 * - if the object has not yet been created - creates it and returns
	 *
	 * Возвращает инстациируемый объект:
	 * - если объект уже был создан - возвращает его
	 * - если объект еще не был создан - создает его и возвращает
	 *
	 * @param mixed $params class constructor parameters
	 * @return static object being instantiated
	 */
	public static function I($params = null) {

		$class = get_called_class();

		global $CONFIG;
		if ($CONFIG['redirects'][$class]) {
			return $CONFIG['redirects'][$class]::I($params);
		}

		$hash = null;
		if (empty($params)) {
			$hash = 'DEFAULT';
		} elseif (is_string($params) or is_numeric($params)) {
			$hash = (string)$params;
		} else {
			$hash = md5(serialize($params));
		}

		if (!isset(self::$instance[$class][$hash])) {
			self::$instance[$class][$hash] = new $class($params);
			self::$instance[$class][$hash]->instanced = true;
		}
		return self::$instance[$class][$hash];
	}

	/**
	 * @param mixed $params ...
	 * @return static ...
	 */
	public static function InstanceDefault($params = null) {
		$Object = self::I($params);
		$class = get_class($Object);
		self::$instance[$class]['DEFAULT'] = $Object;
		return $Object;
	}
}