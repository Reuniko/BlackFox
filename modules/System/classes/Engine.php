<?php

namespace System;

class Engine extends Instanceable {
	public $config = [];
	public $cores = [];
	public $roots = [];
	public $classes = [];
	public $url = [];

	public $DB;

	public $BUFFER = [];
	public $CONTENT = "";
	public $TITLE = "";
	public $SECTION = [];
	public $TEMPLATE = "bootstrap";
	public $TEMPLATE_PATH = "";
	public $WRAPPER = "wrapper";

	protected $initialized = false;

	public function Init() {
		// prevent double run
		if ($this->initialized) {
			throw new Exception("Engine already initialized");
		}
		$this->initialized = true;

		// Init user session
		session_start();

		// read main config
		$this->config = require($_SERVER["DOCUMENT_ROOT"] . '/config.php');
		$this->roots = $this->config['roots'];
		$this->cores = $this->config['cores'];

		// Init autoload classes
		spl_autoload_register([$this, 'AutoloadClass']);
		$this->RegisterModuleClasses('System');

		// Init database connector
		$this->DB = Database::Instance($this->config['database']);
	}

	/**
	 * Checks access for section
	 *
	 * @param array $rules section rules: ['<group_code>' => '<true\false>', ...]
	 * @param array $groups user group codes: ['<group_code>', ...]
	 * @return bool true - allow, false - disallow
	 */
	public function CheckSectionAccess($rules = [], $groups = []) {
		$rules['*'] = isset($rules['*']) ? $rules['*'] : true;
		if ($rules['*'] === true) {
			return true;
		}
		unset($rules['*']);
		foreach ($rules as $rule_group => $rule_right) {
			if ($rule_right === true) {
				if (in_array($rule_group, $groups)) {
					return true;
				}
			}
		}
		return false;
	}

	public function ShowAuthForm($message = null) {
		$this->WRAPPER = 'frame';
		$this->Component('System', 'Authorization', '', [
			'MESSAGE' => $message,
		]);
	}

	public function Work() {

		// Load section info
		$this->url = parse_url($_SERVER['REQUEST_URI']);
		$this->SECTION = require($this->SearchAncestorFile($this->url['path'], '.section.php'));
		$this->TEMPLATE = isset($this->SECTION['TEMPLATE']) ? $this->SECTION['TEMPLATE'] : $this->config['template'];
		$this->WRAPPER = isset($this->SECTION['WRAPPER']) ? $this->SECTION['WRAPPER'] : $this->WRAPPER;
		$this->TEMPLATE_PATH = $this->GetCoreDir('templates/' . $this->TEMPLATE, true);

		// Check access
		$allow = $this->CheckSectionAccess(
			$this->SECTION['ACCESS'] ?: [],
			$_SESSION['USER']['GROUPS'] ?: []
		);

		// Init other modules
		$this->LoadModules();

		// Init TITLE from .section.php
		$this->TITLE = $this->SECTION['NAME'];

		// generate CONTENT
		ob_start();
		if ($allow) {
			$this->ShowContent();
		} else {
			$this->ShowAuthForm("Access denied");
		}
		$this->CONTENT = ob_get_clean();

		// Launch wrapper if it needs
		$wrap_content = (!empty($this->TEMPLATE) and !empty($this->WRAPPER));
		if ($wrap_content) {
			// input:
			// $this->CONTENT
			// $this->TITLE
			// $this->TEMPLATE_PATH
			require($this->GetCoreFile('templates/' . $this->TEMPLATE . '/' . $this->WRAPPER . '.php'));
		} else {
			echo $this->CONTENT;
		}

		// Do last triggers
		// ...
	}

	public function ShowContent() {

		try {
			// запрос на конкретный скрипт
			foreach ($this->roots as $root) {
				$request_path = $root . $this->url['path'];
				if (file_exists($request_path) and !is_dir($request_path)) {
					require($request_path);
					return;
				}
			}

			// запрос на директорию с index.php
			foreach ($this->roots as $root) {
				$request_path = $root . $this->url['path'];
				if (is_dir($request_path) and file_exists($request_path . 'index.php')) {
					require($request_path . 'index.php');
					return;
				}
			}

			// запрос на неизвестный адрес
			require($this->SearchAncestorFile($this->url['path'], '.controller.php'));

		} catch (ExceptionFileNotFound $error) {
			$this->Show404();
		} catch (ExceptionAccessDenied $error) {
			$this->Show403();
		} catch (Exception $error) {
			$this->ShowErrors($error->getArray());
		} catch (\Exception $error) {
			$this->ShowErrors([$error->getMessage()]);
		}
	}

	public function ShowErrors($errors = []) {
		if (!is_array($errors)) {
			$errors = [$errors];
		}
		require($this->GetCoreFile('templates/' . $this->TEMPLATE . '/errors.php'));
	}

	public function Show404() {
		header('HTTP/1.0 404 Not Found');
		$this->ShowErrors(['404 Not Found']);
	}

	public function Show403() {
		header('HTTP/1.0 403 Forbidden');
		$this->ShowErrors(['403 Forbidden']);
	}

	public function AutoloadClass($class) {
		if (isset($this->classes[$class])) {
			require_once($this->classes[$class]);
		}
	}

	public function Debug($data, $title = '', $mode = 'textarea', $target = '/debug.txt') {
		debug($data, $title, $mode, $target);
	}

	/**
	 * Запускает компонент.
	 *
	 * @param string $module символьный код модуля
	 * @param string $component символьный код компонента
	 * @param string $template символьный код шаблона
	 * @param array $params массив параметров компонента
	 * @throws Exception
	 */
	public function Component($module, $component, $template = 'default', $params = []) {

		if (!is_string($module)) {
			throw new Exception("Символьный код модуля должен быть строкой");
		}
		if (!is_string($component)) {
			throw new Exception("Символьный код компонента должен быть строкой");
		}
		if (!is_string($template)) {
			throw new Exception("Символьный код шаблона должен быть строкой");
		}

		if (empty($template)) {
			$template = 'default';
		}
		$path_class = $this->GetCoreFile("modules/{$module}/components/{$component}/{$component}.php");

		$component_class_name = "$module\\$component";

		/** @var Component $component_instance */
		$component_instance = new $component_class_name();
		$component_instance->Init($params);
		$component_instance->component_folder = dirname($path_class);
		$component_instance->template_folder = $component_instance->component_folder . '/templates/' . $template;

		// echo "<!-- $component_class_name -->\r\n";
		// $this->Debug($component_class_name, '$component_class_name');
		if ($component_instance->allow_ajax_request and (in_array($component_class_name, [$_REQUEST['AJAX'], $_REQUEST['ajax']]))) {
			$this->BufferRestart();
			$component_instance->Execute($template);
			$this->BufferFlush();
			die();
		}
		if ($component_instance->allow_json_request and (in_array($component_class_name, [$_REQUEST['JSON'], $_REQUEST['json']]))) {
			$this->BufferRestart();
			echo json_encode($component_instance->ProcessResult());
			$this->BufferFlush();
			die();
		}
		$component_instance->Execute($template);
	}

	public function RegisterModuleClasses($module_name) {
		$this->classes["{$module_name}\\Module"] = $this->GetCoreFile("modules/{$module_name}/Module.php");
		foreach ($this->cores as $core) {
			$files = [];
			$files += $this->ScanDirectoryRecursive("{$core}/modules/{$module_name}/classes");
			$files += $this->ScanDirectoryRecursive("{$core}/modules/{$module_name}/components", 1);
			foreach ($files as $path => $file) {
				if ($file['extension'] === 'php') {
					$this->classes[$module_name . '\\' . $file['filename']] = $path;
				}
			}
		}
	}

	/**
	 * Выбирает информацию обо всех файлах в указанной директории и поддиректориях.
	 *
	 * @param string $directory абсолютный путь к директории
	 * @param int $depth глубина сканирования (не обязательно)
	 * @return array список всех файлов в виде структур pathinfo: {dirname, basename, extension, filename}
	 */
	public function ScanDirectoryRecursive($directory, $depth = null) {
		$files = [];
		$names = @scandir($directory);
		if (!empty($names)) {
			foreach ($names as $name) {
				if ($name === '.' or $name === '..') {
					continue;
				}
				if (is_dir("{$directory}/{$name}")) {
					if ($depth === 0) {
						continue;
					}
					$files += $this->ScanDirectoryRecursive("{$directory}/{$name}", $depth ? ($depth - 1) : $depth);
				} else {
					$files["{$directory}/{$name}"] = pathinfo("{$directory}/{$name}");
				}
			}
		}
		return $files;
	}

	public function GetCoreFile($path) {
		foreach ($this->cores as $core) {
			$full_path = "{$core}/{$path}";
			if (file_exists($full_path)) {
				return $full_path;
			}
		}
		throw new Exception("Found no file '{$path}' in cores");
	}

	/**
	 * Ищет директорию в ядре.
	 * Возвращает к ней путь в зависимости от флага:
	 * - абсолютный
	 * - относительно корня сайта
	 * Генерирует исключение в случае если директория не найдена.
	 *
	 * @param string $path путь к директории относительно корня ядра
	 * @param bool $relative возвращаемый путь: true - относительный, false - абсолютный
	 * @return string относительный\абсолютный путь к директории
	 * @throws Exception директория не найдена
	 */
	public function GetCoreDir($path, $relative = false) {
		foreach ($this->cores as $relative_path => $absolute_path) {
			$full_path = "{$absolute_path}/{$path}";
			if (is_dir($full_path)) {
				if (!$relative) {
					return $full_path;
				} else {
					return "{$relative_path}/{$path}";
				}
			}
		}
		throw new Exception("Found no dir '{$path}' in cores: " . print_r($this->cores, true));
	}

	public function BufferRestart() {
		ob_end_clean();
		$this->BUFFER = [];
	}

	public function BufferFlush() {
		ob_get_flush();
	}

	public function LoadModules() {

		try {
			$modules = Unit::I()->GetList();
		} catch (\Exception $error) {
			Unit::I()->Synchronize();
			Unit::I()->Create(['ID' => 'System']);
			$modules = Unit::I()->GetList();
		}

		foreach ($modules as $module) {
			$class = $module['ID'];
			if ($class === 'System') {
				// already registered
				continue;
			}

			$this->RegisterModuleClasses($module['ID']);
		}
	}

	/**
	 * Ищет ближайший файл вверх по иерархии директорий.
	 *
	 * @param string $uri относительный путь к директории
	 * @param string $filename имя искомого файла
	 * @return string абсолютный путь к искомому файлу (если файл найден)
	 * @throws Exception файл не найден
	 */
	public function SearchAncestorFile($uri, $filename) {
		if (empty($filename)) {
			throw new Exception("Specify \$filename");
		}
		$folders = array_filter(explode('/', $uri));
		//$this->Debug($folders, '$folders');
		$path = '/';
		$paths = ['/' . $filename];
		foreach ($folders as $folder) {
			$path = $path . $folder . '/';
			$paths[] = $path . $filename;
		}
		$paths = array_reverse($paths);
		//$this->Debug($paths, '$paths');
		//$this->Debug($this->roots, '$this->roots');
		foreach ($paths as $path) {
			foreach ($this->roots as $root) {
				if (file_exists($root . $path)) {
					$file = $root . $path;
					return $file;
				}
			}
		}
		throw new ExceptionFileNotFound("Found no '{$filename}' in '{$uri}'");
	}

	/**
	 * Выполняет PHP-файл.
	 * Возвращает имя последнего определенного класса.
	 *
	 * @param string $file php-файл
	 * @return string имя последнего определенного класса
	 * @throws Exception новые классы не были определены
	 * @deprecated use PSR-0 lol
	 */
	public function DetectClassNameFromFile($file) {
		$classes1 = get_declared_classes();
		require($file);
		$classes2 = get_declared_classes();
		//$this->Debug($classes1, '$classes1');
		//$this->Debug($classes2, '$classes2');
		$diff = array_diff($classes2, $classes1);
		//$this->Debug($diff, '$diff');
		unset($classes1);
		unset($classes2);
		if (empty($diff)) {
			throw new Exception("No new classes found");
		}
		$class = end($diff);
		return $class;
	}
}