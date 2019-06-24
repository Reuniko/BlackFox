<?php

namespace System;

class Engine extends Instanceable {

	public $config = [];
	public $cores = [];
	public $roots = [];
	public $modules = [];
	public $classes = [];
	public $url = [];
	public $languages = [];

	/** @var Database $DB */
	public $DB;
	/** @var User $USER */
	public $USER;

	public $TITLE = "";
	public $KEYWORDS = "";
	public $DESCRIPTION = "";

	public $HEADERS = [];
	public $CONTENT = "";
	public $BREADCRUMBS = [];

	public $SECTION = [];
	public $TEMPLATE = "";
	public $TEMPLATE_PATH = "";
	public $WRAPPER = "wrapper";

	public $DELAYED = [];

	/**
	 * Engine constructor:
	 * - Initializes (and prolongs) user session
	 * - Reads main config into $this->config, $this->roots and $this->cores
	 * - Links autoload class system to $this->AutoloadClass()
	 * - Loads module 'System'
	 * - Initializes the main connection to the default database
	 *
	 * @throws Exception
	 */
	public function __construct() {
		$this->InitUserSession();
		$this->InitMainConfig();
		$this->InitAutoloadClasses();
		$this->RegisterModuleClasses('System');
		$this->InitDatabase();
	}

	/**
	 * Initializes (and prolongs) user session
	 */
	public function InitUserSession() {
		session_start();
		$lifetime = 7 * 24 * 60 * 60;
		setcookie(session_name(), session_id(), time() + $lifetime, '/');
	}

	/**
	 * Reads main config into $this->config, $this->roots and $this->cores
	 */
	public function InitMainConfig() {
		$this->config = require($_SERVER["DOCUMENT_ROOT"] . '/config.php');
		$this->roots = $this->config['roots'];
		$this->cores = $this->config['cores'];
		$this->modules = $this->config['modules'];
		$this->languages = $this->config['languages'];
	}

	/**
	 * Links autoload class system to $this->AutoloadClass()
	 */
	public function InitAutoloadClasses() {
		spl_autoload_register([$this, 'AutoloadClass']);
	}

	/**
	 * Initializes the main connection to the default database
	 */
	public function InitDatabase() {
		/** @var Database $DB */
		$DB = Database::InstanceDefault($this->config['database']);
		$this->DB = $DB;
	}

	/**
	 * Checks access for section:
	 * - if user has no access - throws an exception
	 * - if user has access - does nothing
	 *
	 * - group '*' means 'everyone'
	 * - group '@' means 'authorized'
	 *
	 * @param array $rules section rules: ['<group_code>' => '<true\false>', ...]
	 * @internal object $this->USER
	 * @throws ExceptionAccessDenied
	 * @throws ExceptionAuthRequired
	 */
	public function CheckSectionAccess($rules = []) {
		$rules = $rules ?: [];

		$rules['*'] = isset($rules['*']) ? $rules['*'] : true;
		if ($rules['*'] === true) {
			return;
		}
		unset($rules['*']);

		if ($rules['@'] === true) {
			if ($this->USER->IsAuthorized()) {
				return;
			}
		}

		foreach ($rules as $rule_group => $rule_right) {
			if ($rule_right === true) {
				if (in_array($rule_group, $this->USER->GROUPS ?: [])) {
					return;
				}
			}
		}
		if ($this->USER->IsAuthorized()) {
			throw new ExceptionAccessDenied('This section requires higher privileges');
		} else {
			throw new ExceptionAuthRequired('This section requires authorization');
		}
	}

	/**
	 * Loads all Engine properties associated with requested page:
	 * - SECTION - array content of closest ancestor '.section.php' file
	 * - TEMPLATE - symbol code of template, defined by section
	 * - WRAPPER - symbol code of wrapper, defined by section
	 * - TEMPLATE_PATH - relative path to root of template
	 * - TITLE - title of section
	 *
	 * All these properties can be reset in the future execution of the script!
	 */
	public function LoadSectionInfo() {
		$this->url = parse_url($_SERVER['REQUEST_URI']);
		$path_to_section_config = $this->SearchAncestorFile($this->url['path'], '.section.php');
		$this->SECTION = !empty($path_to_section_config) ? require($path_to_section_config) : [];
		$this->TEMPLATE = isset($this->SECTION['TEMPLATE']) ? $this->SECTION['TEMPLATE'] : $this->config['template'];
		$this->WRAPPER = isset($this->SECTION['WRAPPER']) ? $this->SECTION['WRAPPER'] : $this->WRAPPER;
		$this->TEMPLATE_PATH = $this->GetCoreDirectoryRelative('templates/' . $this->TEMPLATE);
		$this->TITLE = $this->SECTION['NAME'];
	}

	public function GetUrlPathParts() {
		return array_filter(explode('/', $this->url['path']));
	}

	/**
	 * Loads the default instance of the user
	 */
	public function LoadUser() {
		/** @var User $USER */
		$USER = User::InstanceDefault();
		$this->USER = $USER;
	}

	/**
	 * Main entry point of the Engine:
	 *
	 * - Loads all properties associated with requested page
	 * - Loads all active modules
	 * - Loads the default instance of the user
	 *
	 * - Generates the content by using method $this->ShowContent()
	 * - If needed, generates wrapper and puts content into it
	 * - Outputs the result of the work
	 */
	public function Work() {

		$this->LoadSectionInfo();
		$this->LoadModules();
		$this->LoadUser();

		$this->SetContent();
		$this->WrapContent();
		$this->ProcessDelayed();

		echo $this->CONTENT;
	}

	/**
	 * Launch wrapper if it needs
	 */
	public function WrapContent() {
		if (empty($this->TEMPLATE) or empty($this->WRAPPER)) {
			return;
		}
		ob_start();
		require($this->GetCoreFile('templates/' . $this->TEMPLATE . '/' . $this->WRAPPER . '.php'));
		$this->CONTENT = ob_get_clean();
	}

	/**
	 * For each row in $this->DELAYED calls the callable and replaces designation (in content) with result of call
	 */
	public function ProcessDelayed() {
		foreach ($this->DELAYED as $delayed) {
			$insert = call_user_func_array($delayed['CALLABLE'], $delayed['PARAMS']);
			$this->CONTENT = str_replace($delayed['TEMPLATE'], $insert, $this->CONTENT);
		}
	}

	/**
	 * Resets current Engine's template and the root path to it
	 * @param string $template symbol code of new template
	 * @throws Exception
	 */
	public function SetTemplate($template) {
		$this->TEMPLATE = $template;
		$this->TEMPLATE_PATH = $this->GetCoreDirectoryRelative('templates/' . $this->TEMPLATE);
	}

	/**
	 * Adds style file to headers.
	 * @param string $path relative/full path to the style file
	 */
	public function AddHeaderStyle($path) {
		$path_absolute = $_SERVER['DOCUMENT_ROOT'] . $path;
		$version = !file_exists($path_absolute) ? '' : '?' . filemtime($path_absolute);
		$this->HEADERS[$path] = [
			'TYPE'   => 'STYLE',
			'PATH'   => $path,
			'STRING' => "<link rel='stylesheet' href='{$path}{$version}'/>",
		];
	}

	/**
	 * Adds script file to headers.
	 * @param string $path relative/full path to the script file
	 */
	public function AddHeaderScript($path) {
		$path_absolute = $_SERVER['DOCUMENT_ROOT'] . $path;
		$version = !file_exists($path_absolute) ? '' : '?' . filemtime($path_absolute);
		$this->HEADERS[$path] = [
			'TYPE'   => 'SCRIPT',
			'PATH'   => $path,
			'STRING' => "<script src='{$path}{$version}'></script>",
		];
	}

	/**
	 * Adds any string to headers.
	 * @param string $string arbitrary string
	 */
	public function AddHeaderString($string) {
		$this->HEADERS[] = [
			'TYPE'   => 'STRING',
			'STRING' => $string,
		];
	}

	/**
	 * Makes and returns printable string of html header,
	 * combining Engine's HEADERS property together.
	 *
	 * @return string
	 */
	public function MakeHeader() {
		$strings = [];
		foreach ($this->HEADERS as $header) {
			$strings[] = $header['STRING'];
		}
		return implode("\r\n\t", $strings);
	}

	/**
	 * Makes and returns a designation of delayed call for MakeHeader method
	 */
	public function GetHeader() {
		return $this->AddDelayedCall([$this, 'MakeHeader']);
	}

	/**
	 * Adds row to $this->DELAYED,
	 * returns a designation to print, witch will be replaced later with the result of callable
	 *
	 * @param mixed $callable
	 * @param array $params (optional)
	 * @return string designation to print
	 */
	public function AddDelayedCall($callable, $params = []) {
		$id = uniqid();
		$template = "[[[DELAYED_{$id}]]]";
		$this->DELAYED[$id] = [
			'TEMPLATE' => $template,
			'CALLABLE' => $callable,
			'PARAMS'   => $params,
		];
		return $template;
	}

	/**
	 * This method tries to generate main content of the page with several steps:
	 * - if requested file exist - executes it and exit
	 * - if requested directory with file 'index.php' exist - executes it and exit
	 * - if there are somewhere in ancestors file '.router.php' exist - executes it and exit
	 * - if redirect exist - does redirect and die
	 * - if content page exist - prints it and exit
	 * If nothing works - throws ExceptionPageNotFound
	 * @throws ExceptionPageNotFound
	 * @throws Exception
	 */
	public function MakeContent() {
		$lang = $this->GetLanguage();

		// request for specific file or directory with 'index.php'
		foreach ($this->roots as $root) {
			$request_path = $root . $this->url['path'];
			if (is_dir($request_path)) {
				$request_path .= 'index.php';
			}
			if ($lang) {
				$pathinfo = pathinfo($request_path);
				$requested_path_lang = "{$pathinfo['dirname']}/{$pathinfo['filename']}.{$lang}.{$pathinfo['extension']}";
				if (file_exists($requested_path_lang)) {
					require($requested_path_lang);
					return;
				}
			}
			if (file_exists($request_path)) {
				require($request_path);
				return;
			}
		}

		// request for non-existing file
		$path_to_router = $this->SearchAncestorFile($this->url['path'], '.router.php');
		if ($path_to_router) {
			require($path_to_router);
			return;
		}

		// redirect
		$redirect = Redirects::I()->Read(['URL' => $this->url['path']]);
		if ($redirect) {
			Redirects::I()->Update($redirect['ID'], ['COUNT' => $redirect['COUNT'] + 1]);
			header("Location: {$redirect['REDIRECT']}");
			die();
		}

		// content from database
		$page = Content::I()->Read(['URL' => $this->url['path']]);
		if ($page) {
			$this->TITLE = $page['TITLE'];
			$this->KEYWORDS = $page['KEYWORDS'];
			$this->DESCRIPTION = $page['DESCRIPTION'];
			echo htmlspecialchars_decode($page['CONTENT']);
			return;
		}

		throw new ExceptionPageNotFound();
	}

	/**
	 * This method unconditionally displays the content for the page, using $this->MakeContent().
	 * If any exception occurs during the process of content generation -
	 * catches it and launches the corresponding alternative method:
	 * - ShowErrors
	 * - ShowAuthForm
	 * - Show404
	 * - Show403
	 */
	public function ShowContent() {
		try {

			$this->CheckSectionAccess($this->SECTION['ACCESS']);
			$this->MakeContent();

		} catch (ExceptionSQL $Exception) {
			$messages = [];
			$messages[] = 'SQL QUERY ERROR: ' . $Exception->getMessage();
			if (User::I()->InGroup('root')) {
				$messages[] = "<pre>{$Exception->SQL}</pre>";
			}
			$this->ShowErrors($messages);
		} catch (ExceptionAuthRequired $Exception) {
			$this->ShowAuthForm($Exception->getMessage());
		} catch (ExceptionPageNotFound $Exception) {
			$this->Show404();
		} catch (ExceptionAccessDenied $Exception) {
			$this->Show403();
		} catch (Exception $Exception) {
			$this->ShowErrors($Exception->getArray());
		} catch (\Exception $Exception) {
			$this->ShowErrors([$Exception->getMessage()]);
		}
	}

	/**
	 * Sets $this->CONTENT
	 */
	public function SetContent() {
		ob_start();
		$this->ShowContent();
		$this->CONTENT = ob_get_clean();
	}

	/**
	 * Launches auth unit with no frame
	 * @param string $message reason to auth
	 */
	public function ShowAuthForm($message = null) {
		$this->WRAPPER = 'frame';
		\System\Authorization::Run(['MESSAGE' => $message, 'REDIRECT' => '']);
	}

	/**
	 * This method tries to show passed array of errors,
	 * using file 'errors.php' from the current template root folder.
	 * Otherwise displays them as plain divs.
	 * @param string|array $errors
	 */
	public function ShowErrors($errors = []) {
		if (!is_array($errors)) {
			$errors = [$errors];
		}

		if (!empty($this->TEMPLATE)) {
			try {
				require($this->GetCoreFile('templates/' . $this->TEMPLATE . '/errors.php'));
				return;
			} catch (Exception $error) {
				// no 'errors.php' found, it's okay
			}
		}

		foreach ($errors as $error) {
			echo "<div>{$error}</div>";
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

	/**
	 * Auto-loader for classes.
	 * All classes stores in $this->classes array, where:
	 * - key - is a full class name with namespace
	 * - value - absolute path to the file with class definition
	 *
	 * @param string $class class than needs to be loaded
	 */
	public function AutoloadClass($class) {
		if (isset($this->classes[$class])) {
			require_once($this->classes[$class]);
		}
	}

	/**
	 * Searches for all classes of the module and registers them in the engine, filling the array $this->classes:
	 * - key - class name (along with namespace)
	 * - value - file path
	 *
	 * @param string $namespace symbolic code of the module
	 * @throws Exception
	 */
	public function RegisterModuleClasses($namespace) {
		$Module = "{$namespace}\\Module";
		$this->classes[$Module] = $this->GetCoreFile("modules/{$namespace}/Module.php");
		if (!is_subclass_of($Module, 'System\AModule')) {
			throw new Exception(T([
				'en' => "Module '{$namespace}' must be the child of 'System\AModule'",
				'ru' => "Модуль '{$namespace}' должен быть наследником 'System\AModule'",
			]));
		}
		/**@var AModule $Module */
		$this->classes += $Module::I()->GetClasses();
	}

	/**
	 * Search and return absolute path to the file, first among all active cores.
	 * If no file found - throws an exception.
	 *
	 * @param string $path path to file, relative to any core root
	 * @return string absolute path to file
	 * @throws Exception Found no file '___' in cores
	 */
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
	 * Search and return absolute path to the directory, first among all active cores.
	 * If no directory found - throws an exception.
	 *
	 * @param string $path path to directory, relative to any core root
	 * @return string absolute path to directory
	 * @throws Exception Found no directory '___' in cores
	 */
	public function GetCoreDirectoryAbsolute($path) {
		foreach ($this->cores as $relative_path => $absolute_path) {
			$full_path = "{$absolute_path}/{$path}";
			if (is_dir($full_path)) {
				return $full_path;
			}
		}
		throw new Exception("Found no directory '{$path}' in cores");
	}

	/**
	 * Search and return relative path to the directory, first among all active cores.
	 * If no directory found - throws an exception.
	 *
	 * @param string $path path to directory, relative to any core root
	 * @return string path to directory, relative server root
	 * @throws Exception Found no directory '___' in cores
	 */
	public function GetCoreDirectoryRelative($path) {
		foreach ($this->cores as $relative_path => $absolute_path) {
			$full_path = "{$absolute_path}/{$path}";
			if (is_dir($full_path)) {
				return "{$relative_path}/{$path}";
			}
		}
		throw new Exception("Found no directory '{$path}' in cores");
	}

	/**
	 * Register all active modules,
	 * except module 'System' cause it's been loaded already.
	 */
	public function LoadModules() {
		foreach ($this->modules as $namespace) {
			if ($namespace === 'System') {
				continue; // already registered
			}
			$this->RegisterModuleClasses($namespace);
		}
	}

	/**
	 * Upgrade all active modules:
	 * for every active module - launches it's Upgrade() method.
	 */
	public function UpgradeActiveModules() {
		foreach ($this->modules as $namespace) {
			$module = "{$namespace}\\Module";
			/* @var \System\AModule $module */
			$module::I()->Upgrade();
		}
	}

	/**
	 * Search for the nearest file up the hierarchy of directories.
	 *
	 * @param string $uri path to directory, relative server root
	 * @param string $filename the name of the file to search for
	 * @return null|string absolute path to the search file (if file found), null (if no file found)
	 * @throws Exception
	 */
	public function SearchAncestorFile($uri, $filename) {
		if (empty($uri)) {
			throw new Exception("Specify uri");
		}
		if (empty($filename)) {
			throw new Exception("Specify filename");
		}

		$folders = array_filter(explode('/', $uri));
		$path = '/';
		$paths = ['/' . $filename];
		foreach ($folders as $folder) {
			$path = $path . $folder . '/';
			$paths[] = $path . $filename;
		}
		$paths = array_reverse($paths);
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
	 * Converts absolute path to path, relative server root or specified root
	 *
	 * @param string $absolute_path absolute path
	 * @param string $root_path root path (optional, server root by default)
	 * @return string relative path
	 * @throws Exception
	 */
	public function GetRelativePath($absolute_path, $root_path = null) {
		$root_path = $root_path ?: $_SERVER['DOCUMENT_ROOT'];
		if (strpos($absolute_path, $root_path) === false) {
			throw new Exception("Can't find relative path for absolute path '{$absolute_path}'");
		}
		$relative_path = str_replace($root_path, '', $absolute_path);
		return $relative_path;
	}

	/**
	 * Adds breadcrumbs to the end of the chain.
	 * If the link is not specified, it takes a link to the current request (SERVER REQUEST_URI)
	 *
	 * @param string $name breadcrumb name
	 * @param string $link breadcrumb link (optional)
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

	public function GetLanguage() {
		$_lang = &$_SESSION['USER']['LANGUAGE'];
		if (!empty($_lang)) {
			return $_lang;
		}
		if (is_object($this->USER) and $this->USER->IsAuthorized()) {
			$_lang = $this->USER->FIELDS['LANGUAGE'];
		} else {
			$_lang = reset(array_keys($this->languages));
		}
		return $_lang;
	}

	public function SetLanguage(string $language) {
		if (!isset($this->languages[$language])) {
			throw new Exception("Language '{$language}' not found");
		}
		$_SESSION['USER']['LANGUAGE'] = $language;
		if ($this->USER->IsAuthorized()) {
			Users::I()->Update($this->USER->ID, ['LANGUAGE' => $language]);
		}
	}
}