<?php

namespace BlackFox;

trait Instance {

	/**
	 * Defines global overrides
	 *
	 * @param array $overrides array of classes to be overridden:
	 * key - string, name of the interface or abstract class or concrete class (which must be overridden);
	 * value - string, name of the final class (with trait Instance);
	 * @throws Exception Wrong override...
	 */
	public static function AddOverrides(array $overrides) {
		foreach ($overrides as $old_class_name => $new_class_name) {
			if ($old_class_name === $new_class_name) {
				throw new Exception("Wrong override: '{$old_class_name}' => '{$new_class_name}'");
			}
			self::$overrides[$old_class_name] = $new_class_name;
		}
	}

	/**
	 * @var self[] $overrides array of classes to be overridden:
	 * key - string, name of the interface or abstract class or concrete class (which should be overridden);
	 * value - string, name of the final class (with trait Instance);
	 */
	private static $overrides = [];

	/** @var self[] array of instantiated classes: key - class name, value - Object */
	private static $instances = [];

	/** @var bool if the class has been instanced - in most cases it is required to prohibit a change in its internal state */
	protected $is_instanced = false;

	/**
	 * Returns the object being instantiated:
	 * - if the object has already been created - returns it
	 * - if the object has not yet been created - creates it and returns
	 *
	 * @return static object being instantiated
	 */
	public static function I() {
		/** @var self|string $class */
		$class = get_called_class();
		if (self::$overrides[$class]) {
			return self::$overrides[$class]::I();
		}
		if (isset(self::$instances[$class])) {
			return self::$instances[$class];
		}
		self::$instances[$class] = $class::N();
		self::$instances[$class]->is_instanced = true;
		return self::$instances[$class];
	}

	/**
	 * Creates and returns a new instance of this class,
	 * filling all __construct parameters with values from self::$overrides
	 *
	 * ```php
	 * $Object = Class::N();
	 * $Object->Method();
	 * ```
	 *
	 * @return static object
	 * @throws Exception
	 * @throws \ReflectionException
	 */
	public static function N() {
		$class = get_called_class();
		if (self::$overrides[$class]) {
			return self::$overrides[$class]::N();
		}

		$ReflectionClass = new \ReflectionClass(get_called_class());
		try {
			$Parameters = $ReflectionClass->getMethod('__construct')->getParameters();
		} catch (\ReflectionException $error) {
			$Parameters = [];
		}

		$args = [];
		foreach ($Parameters as $Parameter) {
			if (!$Parameter->hasType()) {
				throw new Exception("Construct parameter '{$Parameter->getName()}' must have a type");
			}
			$Type = $Parameter->getType()->getName();
			if (isset(self::$overrides[$Type])) {
				$args[$Type] = self::$overrides[$Type]::I();
			} else {
				$args[$Type] = $Type::I();
			}
		}
		return $ReflectionClass->newInstanceArgs($args);
	}
}