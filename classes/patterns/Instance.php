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
			Instance::$overrides[$old_class_name] = $new_class_name;
		}
	}

	/**
	 * @var Instance[] $overrides array of classes to be overridden:
	 * key - string, name of the interface or abstract class or concrete class (which should be overridden);
	 * value - string, name of the final class (with trait Instance);
	 */
	public static $overrides = null;

	/** @var Instance[] array of instantiated classes: key - class name, value - Object */
	public static $instances = [];

	/** @var bool if the class has been instanced - in most cases it is required to prohibit a change in its internal state */
	public $is_global_instance = false;

	/**
	 * Returns the global instance of this class
	 *
	 * ```php
	 * Class::I()->Method();
	 * ```
	 *
	 * @param array $params
	 * @return static global instance
	 * @throws Exception
	 * @throws \ReflectionException
	 */
	public static function I($params = []) {
		if (Instance::$overrides === null) {
			Instance::$overrides = [];
			$config = Engine::GetConfig();
			Instance::AddOverrides($config['overrides'] ?: []);
		}

		/** @var Instance|string $class */
		$class = get_called_class();
		if (Instance::$overrides[$class]) {
			return Instance::$overrides[$class]::I($params);
		}
		if (isset(Instance::$instances[$class])) {
			if (empty($params)) {
				return Instance::$instances[$class];
			} else {
				throw new Exception("Can't initiate global instance of class '{$class}': global instance already exist");
			}
		}
		Instance::$instances[$class] = $class::N($params);
		Instance::$instances[$class]->is_global_instance = true;
		return Instance::$instances[$class];
	}

	/**
	 * Creates and returns a new instance of this class,
	 * by filling all __construct parameters with:
	 * - $params
	 * - global instances matching parameter type
	 *
	 * ```php
	 * $Object = Class::N();
	 * $Object->Method();
	 * ```
	 *
	 * @param array $params
	 * @return static local instance
	 * @throws Exception
	 * @throws \ReflectionException
	 */
	public static function N($params = []) {
		$class = get_called_class();
		if (Instance::$overrides[$class]) {
			return Instance::$overrides[$class]::N();
		}

		$ReflectionClass = new \ReflectionClass(get_called_class());
		try {
			$Parameters = $ReflectionClass->getMethod('__construct')->getParameters();
		} catch (\ReflectionException $error) {
			$Parameters = [];
		}

		$args = [];
		foreach ($Parameters as $Parameter) {
			// $params is set: $args from $params
			if (isset($params[$Parameter->getName()])) {
				$args[$Parameter->getName()] = $params[$Parameter->getName()];
				continue;
			}
			// $Parameter has no type: $args from default value
			if (!$Parameter->hasType()) {
				if ($Parameter->isOptional()) {
					$args[$Parameter->getName()] = $Parameter->getDefaultValue();
					continue;
				} else {
					throw new Exception("Can't construct class '{$class}': non-optional parameter '{$Parameter->getName()}' doesn't have a type");
				}
			}
			// $Parameter has a type
			$ParameterType = $Parameter->getType();
			if ($ParameterType->isBuiltin()) {
				if ($Parameter->isOptional()) {
					$args[$Parameter->getName()] = $Parameter->getDefaultValue();
					continue;
				} else {
					throw new Exception("Can't construct class '{$class}': non-optional parameter '{$Parameter->getName()}' has a builtin type");
				}
			}
			// $Parameter has a non-builtin type
			$p_class = $ParameterType->getName();
			$traits = (new \ReflectionClass($p_class))->getTraits();
			if (isset($traits['BlackFox\Instance'])) {
				/**@var string|self $p_class */
				$args[$p_class] = $p_class::I();
			} else {
				throw new Exception("Can't construct class '{$class}': non-optional parameter '{$Parameter->getName()}' of type '{$p_class}' doesn't have 'BlackFox\Instance' trait");
			}
		}
		return $ReflectionClass->newInstanceArgs($args);
	}
}