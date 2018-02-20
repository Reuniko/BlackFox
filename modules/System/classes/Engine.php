<?php

namespace System;

class Engine extends Instanceable {
	public $config = [];
	public $cores = [];
	public $roots = [];
	public $modules = [];
	public $classes = [];
	public $url = [];

	public $DB;
	/** @var User */
	public $USER;

	public $TITLE = "";
	public $HEADER = "";
	public $HEADERS = [];
	public $CONTENT = "";
	public $BREADCRUMBS = [];

	public $SECTION = [];
	public $TEMPLATE = "";
	public $TEMPLATE_PATH = "";
	public $WRAPPER = "wrapper";

	protected $initialized = false;

	public function __construct() {
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
		$this->DB = Database::InstanceDefault($this->config['database']);
	}

	/**
	 * Checks access for section
	 *
	 * @param array $rules section rules: ['<group_code>' => '<true\false>', ...]
	 * @param array $groups user group codes: ['<group_code>', ...]
	 * @throws ExceptionAccessDenied
	 * @throws ExceptionAuthRequired
	 */
	public function CheckSectionAccess($rules = [], $groups = []) {
		$rules = $rules ?: [];
		$groups = $groups ?: [];
		$rules['*'] = isset($rules['*']) ? $rules['*'] : true;
		if ($rules['*'] === true) {
			return;
		}
		unset($rules['*']);
		foreach ($rules as $rule_group => $rule_right) {
			if ($rule_right === true) {
				if (in_array($rule_group, $groups)) {
					return;
				}
			}
		}
		if (User::I()->IsAuthorized()) {
			throw new ExceptionAccessDenied('This section requires higher privileges');
		} else {
			throw new ExceptionAuthRequired('This section requires authorisation');
		}
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
		$this->TITLE = $this->SECTION['NAME'];

		// Init other modules
		$this->LoadModules();

		// setup USER
		/** @var User $USER */
		$USER = User::InstanceDefault();
		$this->USER = $USER;

		// generate CONTENT
		ob_start();
		$this->ShowContent();
		$this->CONTENT = ob_get_clean();

		// Launch wrapper if it needs
		if ((!empty($this->TEMPLATE) and !empty($this->WRAPPER))) {
			// input: $this-> CONTENT, TITLE, TEMPLATE_PATH
			require($this->GetCoreFile('templates/' . $this->TEMPLATE . '/' . $this->WRAPPER . '.php'));
		} else {
			echo $this->CONTENT;
		}

	}

	public function AddHeaderStyle($path) {
		$this->HEADERS[$path] = [
			'TYPE' => 'STYLE',
			'PATH' => $path,
		];
	}

	public function AddHeaderScript($path) {
		$this->HEADERS[$path] = [
			'TYPE' => 'SCRIPT',
			'PATH' => $path,
		];
	}

	public function AddHeaderString($string) {
		$this->HEADERS[] = [
			'TYPE'   => 'STRING',
			'STRING' => $string,
		];
	}

	public function MakeHeader() {
		if (empty($this->HEADERS)) {
			return null;
		}
		$headers = [];
		foreach ($this->HEADERS as $header) {
			if ($header['TYPE'] === 'STRING') {
				$headers[] = $header['STRING'];
				continue;
			}
			$version = null;
			$path_absolute = $_SERVER['DOCUMENT_ROOT'] . $header['PATH'];
			if (file_exists($path_absolute)) {
				// throw new Exception("Header not found: {$header['PATH']}");
				$version = filemtime($path_absolute);
			}
			if ($header['TYPE'] === 'STYLE') {
				$headers[] = "<link rel='stylesheet' href='{$header['PATH']}?{$version}'/>";
				continue;
			}
			if ($header['TYPE'] === 'SCRIPT') {
				$headers[] = "<script src='{$header['PATH']}?{$version}'></script>";
				continue;
			}
			throw new Exception("Unknown header type '{$header['TYPE']}'");
		}
		return implode("\r\n", $headers);
	}

	public function GetHeader() {
		$this->HEADER = $this->HEADER ?: $this->MakeHeader();
		return $this->HEADER;
	}

	public function ShowContent() {
		try {

			// Check access
			$this->CheckSectionAccess($this->SECTION['ACCESS'], $this->USER->GROUPS);

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

			// запрос на контроллер
			$path_to_controller = $this->SearchAncestorFile($this->url['path'], '.controller.php');
			if ($path_to_controller) {
				require($path_to_controller);
				return;
			}

			// запрос на контент в бд
			$page = Content::I()->Read(['URL' => $this->url['path']]);
			if ($page) {
				$this->TITLE = $page['TITLE'];
				echo htmlspecialchars_decode($page['CONTENT']);
				return;
			}

			throw new ExceptionPageNotFound();

		} catch (ExceptionSQL $exception) {
			$message = 'SQL QUERY ERROR: ' . $exception->getMessage();
			if (User::I()->InGroup('root')) {
				$message .= "<br/><br/><pre>{$exception->SQL}</pre>";
			}
			$this->ShowErrors([$message]);
		} catch (ExceptionAuthRequired $exception) {
			$this->ShowAuthForm($exception->getMessage());
		} catch (ExceptionPageNotFound $exception) {
			$this->Show404();
		} catch (ExceptionAccessDenied $exception) {
			$this->Show403();
		} catch (Exception $exception) {
			$this->ShowErrors($exception->getArray());
		} catch (\Exception $exception) {
			$this->ShowErrors([$exception->getMessage()]);
		}
	}

	public function ShowErrors($errors = []) {
		if (!is_array($errors)) {
			$errors = [$errors];
		}
		if (!empty($this->TEMPLATE)) {
			require($this->GetCoreFile('templates/' . $this->TEMPLATE . '/errors.php'));
		} else {
			foreach ($errors as $error) {
				echo $error;
			}
		}
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
	 * @deprecated use Component::Run() instead
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
		$component_class_name = "$module\\$component";
		if (!class_exists($component_class_name)) {
			throw new Exception("Component not found: {$component_class_name}");
		}
		/** @var Component $component_class_name */
		$component_class_name::Run($params, $template);
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
	 * - false - абсолютный
	 * - true - относительно корня сайта
	 *
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
	}

	public function BufferFlush() {
		ob_get_flush();
	}

	public function LoadModules() {

		try {
			$this->modules = Modules::I()->GetList();
		} catch (\Exception $error) {
			Modules::I()->Synchronize();
			Modules::I()->Create(['ID' => 'System']);
			$this->modules = Modules::I()->GetList();
		}

		foreach ($this->modules as $module) {
			$class = $module['ID'];
			if ($class === 'System') {
				// already registered
				continue;
			}

			$this->RegisterModuleClasses($module['ID']);
		}
	}

	public function UpgradeActiveModules() {
		$namespaces = Modules::I()->Select();
		foreach ($namespaces as $namespace) {
			$module = "{$namespace}\\Module";
			/* @var \System\AModule $module */
			$module::I()->Upgrade();
		}
	}

	/**
	 * Ищет ближайший файл вверх по иерархии директорий.
	 *
	 * @param string $uri относительный путь к директории
	 * @param string $filename имя искомого файла
	 * @return string абсолютный путь к искомому файлу (если файл найден)
	 * @throws Exception
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
		return null;
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

	public function GetRelativePath($absolute_path, $root_path = null) {
		$root_path = $root_path ?: $_SERVER['DOCUMENT_ROOT'];
		if (strpos($absolute_path, $root_path) === false) {
			throw new Exception("Can't find relative path for absolute path '{$absolute_path}'");
		}
		$relative_path = str_replace($root_path, '', $absolute_path);
		return $relative_path;
	}

	/**
	 * Добавляет хлебную крошку в конец цепочки.
	 * Если ссылка не указана, берет ссылку на текущий запрос (REQUEST_URI).
	 *
	 * @param string $name имя хлебной крошки
	 * @param string $link ссылка хлебной крошки
	 */
	public function AddBreadcrumb($name, $link = null) {
		if ($link === null) {
			$link = $_SERVER['REQUEST_URI'];
		}
		$this->BREADCRUMBS[] = [
			'NAME' => $name,
			'LINK' => $link,
		];
	}
}