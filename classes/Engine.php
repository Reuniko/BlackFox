<?php

namespace BlackFox;

class Engine {

	use Instance;

	public $config = [];
	public $cores = [];
	public $roots = [];
	public $templates = [];

	/**
	 * dictionary of available classes
	 * - key - is a full class name with namespace
	 * - value - absolute path to the file with class definition
	 * @var array
	 */
	public $classes = [];

	/**
	 * dictionary of available languages
	 * - key - symbolic code of the language
	 * - value - display name of the language
	 * @var array
	 */
	public $languages = [];

	public $url = [];

	/** @var Database $Database */
	public $Database;
	/** @var User $User */
	public $User;
	/** @var Cache $Cache */
	public $Cache;

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

	public function GetConfig(): array {
		return require($_SERVER['DOCUMENT_ROOT'] . '/config.php');
	}

	/**
	 * Engine constructor:
	 * - Initializes (and prolongs) user session
	 * - Reads main config into $this->config, $this->roots and $this->cores
	 * - Links autoload class system to $this->AutoloadClass()
	 * - Loads module 'BlackFox'
	 * - Initializes the main connection to the default database
	 * @throws Exception
	 */
	public function __construct() {
		$this->InitConfig($this->GetConfig());
		$this->InitUserSession();
		$this->InitAutoloadClasses();
		$this->InitExceptionHandler();
		$this->RegisterCoreClasses('BlackFox');
		$this->InitDatabase();
		$this->InitCache();
	}

	/**
	 * Parses main config into props: config, roots, cores, templates, languages.
	 * Adds overrides from $config['overrides'].
	 * @param array $config
	 * @throws Exception
	 */
	public function InitConfig(array $config) {
		$this->config = $config;
		$this->roots = $config['roots'];
		$this->cores = $config['cores'];
		$this->templates = $config['templates'];
		$this->languages = $config['languages'];
		Instance::AddOverrides($config['overrides'] ?: []);
	}

	/**
	 * Initializes and prolongs a user's session
	 */
	public function InitUserSession() {
		session_start();
		$lifetime = 7 * 24 * 60 * 60;
		setcookie(session_name(), session_id(), time() + $lifetime, '/');
	}


	/**
	 * Links autoload class system to $this->AutoloadClass()
	 */
	public function InitAutoloadClasses() {
		spl_autoload_register([$this, 'AutoloadClass']);
	}

	public function InitExceptionHandler() {
		set_exception_handler([$this, 'ExceptionHandler']);
	}

	public function ExceptionHandler(\Throwable $Exception) {
		debug($Exception, '$Exception');
		echo '<xmp>';
		echo $Exception->getMessage();
		echo "\r\n\r\n";
		echo $Exception->getTraceAsString();
		echo '</xmp>';
	}

	/**
	 * Initializes the main connection to the default database
	 */
	public function InitDatabase() {
		$this->Database = Database::I(['params' => $this->config['database']]);
	}

	/**
	 * Initializes the main connection to the default cache
	 */
	public function InitCache() {
		$this->Cache = Cache::I(['params' => $this->config['cache']]);
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
	 * @throws ExceptionAccessDenied
	 * @throws ExceptionAuthRequired
	 * @internal object $this->USER
	 */
	public function CheckSectionAccess($rules = []) {
		$rules = $rules ?: [];

		$rules['*'] = isset($rules['*']) ? $rules['*'] : true;
		if ($rules['*'] === true) {
			return;
		}
		unset($rules['*']);

		if ($rules['@'] === true) {
			if ($this->User->IsAuthorized()) {
				return;
			}
		}

		foreach ($rules as $rule_group => $rule_right) {
			if ($rule_right === true) {
				if (in_array($rule_group, $this->User->GROUPS ?: [])) {
					return;
				}
			}
		}
		if ($this->User->IsAuthorized()) {
			throw new ExceptionAccessDenied(T([
				'en' => 'This section requires higher privileges',
				'ru' => 'Доступ запрещен',
			]));
		} else {
			throw new ExceptionAuthRequired(T([
				'en' => 'This section requires authorization',
				'ru' => 'Для доступа в этот раздел необходима авторизация',
			]));
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
		if ($this->url === false)
			throw new Exception("Can't parse url");

		$path_to_section_config = $this->SearchAncestorFile($this->url['path'], '.section.php');
		$this->SECTION = !empty($path_to_section_config) ? require($path_to_section_config) : [];

		$this->TITLE = isset($this->SECTION['TITLE']) ? $this->SECTION['TITLE'] : $this->TITLE;

		if (isset($this->SECTION['TEMPLATE'])) {
			$this->SetTemplate($this->SECTION['TEMPLATE']);
		}
		if (isset($this->SECTION['WRAPPER'])) {
			$this->SetWrapper($this->SECTION['WRAPPER']);
		}
	}

	/**
	 * Sets props TEMPLATE and TEMPLATE_PATH
	 * @param string $template symbol code of new template
	 * @throws Exception
	 */
	public function SetTemplate($template) {
		if ($template === null) {
			$this->TEMPLATE = null;
			$this->TEMPLATE_PATH = null;
			return;
		}
		if (empty($this->templates[$template])) {
			throw new Exception("Template not found: '{$template}'");
		}
		$this->TEMPLATE = $template;
		$this->TEMPLATE_PATH = $this->templates[$template];
	}

	/**
	 * Sets prop WRAPPER
	 * @param string $wrapper symbol code of new wrapper
	 * @throws Exception
	 */
	public function SetWrapper($wrapper) {
		if ($wrapper === null) {
			$this->WRAPPER = null;
			return;
		}
		if (!empty($wrapper)) {
			$wrapper_file = $this->GetAbsolutePath($this->TEMPLATE_PATH . "/{$wrapper}.php");
			if (!file_exists($wrapper_file)) {
				throw new Exception("Wrapper not found: '{$wrapper}'; of template: '{$this->TEMPLATE}'");
			}
		}
		$this->WRAPPER = $wrapper;
	}

	private function TrimSlashes($string) {
		return implode('/', array_filter(explode('/', $string)));
	}

	/**
	 * Handy to use in .router.php
	 * @param array $keys
	 * @return array of url parts
	 */
	public function ParseUrlPathRelative($keys = []) {
		$file = debug_backtrace()[0]['file'];
		$file = str_replace('\\', '/', $file);
		$info = pathinfo($file);

		$dirname = $info['dirname'];

		$dirname_relative = null;
		foreach ($this->roots as $root_relative_folder => $root_absolute_folder) {
			if (strpos($dirname, $root_absolute_folder) !== false) {
				$dirname_relative = str_replace($root_absolute_folder, '', $dirname);
				break;
			}
		}
		$dirname_relative = $this->TrimSlashes($dirname_relative);

		$path = $this->TrimSlashes($this->url['path']);

		$request_relative = str_replace($dirname_relative, '', $path);
		$request_relative = $this->TrimSlashes($request_relative);

		$result_array = explode('/', $request_relative);
		if (empty($keys)) {
			return $result_array;
		}

		$result_dictionary = [];
		foreach ($keys as $index => $key) {
			$result_dictionary[$key] = $result_array[$index];
		}
		return $result_dictionary;
	}

	/**
	 * Loads the default instance of the user
	 */
	public function InitUser() {
		/** @var User $USER */
		$this->User = User::I();
		$this->User->Init($_SESSION['USER']['ID'] ?: null);
	}

	/**
	 * Main entry point of the Engine:
	 *
	 * - Loads all properties associated with requested page
	 * - Loads the default instance of the user
	 *
	 * - Generates the content by using method $this->ShowContent()
	 * - If needed, generates wrapper and puts content into it
	 * - Outputs the result of the work
	 */
	public function Work() {
		$this->LoadSectionInfo();
		$this->InitUser();

		$this->SetContent();
		$this->WrapContent();
		$this->ProcessDelayed();

		echo $this->CONTENT;
	}

	/**
	 * Launch wrapper
	 * if $this->TEMPLATE and $this->WRAPPER are not empty
	 */
	public function WrapContent() {
		if (empty($this->TEMPLATE) or empty($this->WRAPPER)) {
			return;
		}

		$wrapper = $this->GetAbsolutePath($this->templates[$this->TEMPLATE] . "/{$this->WRAPPER}.php");
		if (!file_exists($wrapper)) {
			throw new Exception("Wrapper file not found: '{$wrapper}'");
		}

		ob_start();
		require($wrapper);
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
		foreach ($this->roots as $root_absolute_folder) {
			$request_path = $root_absolute_folder . $this->url['path'];
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
		header('HTTP/1.0 401 Unauthorized');
		$this->WRAPPER = 'frame';
		$this->TITLE = $message;
		\BlackFox\Authorization::Run(['MESSAGE' => $message]);
	}

	/**
	 * This method tries to show passed array of errors,
	 * using file 'errors.php' from the current template folder.
	 * Otherwise displays them as plain divs.
	 * @param string|array $errors
	 */
	public function ShowErrors($errors = []) {
		if (!is_array($errors)) {
			$errors = [$errors];
		}

		if (!empty($this->TEMPLATE)) {
			$template_errors = $this->GetAbsolutePath($this->templates[$this->TEMPLATE] . "/errors.php");
			if (file_exists($template_errors)) {
				require($template_errors);
				return;
			}
		}

		foreach ($errors as $error) {
			echo "<div class='alert alert-danger'>{$error}</div>";
		}
	}

	public function Show404() {
		header('HTTP/1.0 404 Not Found');
		$this->TITLE = '404 Not Found';
		$this->ShowErrors(['404 Not Found']);
	}

	public function Show403() {
		header('HTTP/1.0 403 Forbidden');
		$this->TITLE = '403 Forbidden';
		$this->WRAPPER = 'frame';
		$this->ShowErrors(['403 Forbidden']);
	}

	/**
	 * Auto-loader for classes.
	 *
	 * @param string $class class that needs to be loaded
	 * @throws Exception
	 * @todo use RegisterCoreClasses once
	 */
	public function AutoloadClass($class) {

		if (isset($this->classes[$class])) {
			require_once($this->classes[$class]);
			return;
		}

		list($namespace) = explode('\\', $class);
		if ($this->cores[$namespace]) {
			$this->RegisterCoreClasses($namespace);
			if (isset($this->classes[$class])) {
				require_once($this->classes[$class]);
				return;
			}
		}
	}

	/**
	 * Searches for all classes of the module and registers them in the engine, filling the array $this->classes
	 *
	 * @param string $namespace symbolic code of the core/namespace
	 * @throws Exception
	 */
	public function RegisterCoreClasses($namespace) {
		$Core = "{$namespace}\\Core";
		$this->classes[$Core] = $this->cores[$namespace] . '/Core.php';
		require_once($this->classes[$Core]);
		if (!is_subclass_of($Core, 'BlackFox\ACore')) {
			throw new Exception(T([
				'en' => "Module '{$namespace}' must be the child of 'BlackFox\ACore'",
				'ru' => "Модуль '{$namespace}' должен быть наследником 'BlackFox\ACore'",
			]));
		}
		/**@var ACore $Core */
		$this->classes += $Core::I()->GetClasses();
		$Core::I()->Load();
	}

	/**
	 * Converts relative path to absolute path
	 *
	 * @param string $relative_path relative path
	 * @return string absolute path
	 */
	public function GetAbsolutePath($relative_path) {
		return $_SERVER['DOCUMENT_ROOT'] . $relative_path;
	}

	/**
	 * Converts absolute path to path, relative to document root or specified root
	 *
	 * @param string $absolute_path absolute path
	 * @param string $root_path root path (optional, document root by default)
	 * @return string relative path
	 * @throws Exception
	 */
	public function GetRelativePath($absolute_path, $root_path = null) {
		$root_path = $root_path ?: $_SERVER['DOCUMENT_ROOT'];

		$root_path = str_replace('\\', '/', $root_path);
		$absolute_path = str_replace('\\', '/', $absolute_path);

		if (strpos($absolute_path, $root_path) === false) {
			throw new Exception("Can't find relative path for absolute path '{$absolute_path}' with root '{$root_path}'");
		}
		$relative_path = str_replace($root_path, '', $absolute_path);
		return $relative_path;
	}

	/**
	 * Upgrade all active cores:
	 * for every active core - launches it's Upgrade() method.
	 */
	public function Upgrade() {
		foreach ($this->cores as $namespace => $core_absolute_folder) {
			$Core = "{$namespace}\\Core";
			/* @var \BlackFox\ACore $Core */
			$Core::I()->Upgrade();
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
			foreach ($this->roots as $root_absolute_folder) {
				if (file_exists($root_absolute_folder . $path)) {
					$file = $root_absolute_folder . $path;
					return $file;
				}
			}
		}
		return null;
	}

	/**
	 * Adds breadcrumbs to the end of the chain.
	 * If the link is not specified, it takes a link to the current request (SERVER REQUEST_URI)
	 *
	 * @param string $name breadcrumb name
	 * @param string $link breadcrumb link (optional)
	 */
	public function AddBreadcrumb($name, $link = null) {
		$this->BREADCRUMBS[] = [
			'NAME' => $name,
			'LINK' => $link,
		];
	}

	public function GetLanguage() {
		$_lang = &$_SESSION['USER']['LANGUAGE'];
		if (!empty($_lang)) return $_lang;

		if (is_object($this->User) and $this->User->IsAuthorized()) {
			$_lang = $this->User->FIELDS['LANGUAGE'];
		} else {
			$_lang = $this->GetDefaultLanguage();
		}

		return $_lang;
	}

	public function GetDefaultLanguage() {
		$browser_language_string = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		if (!empty($browser_language_string)) {
			$browser_language_string = explode(',', $browser_language_string);
			$browser_languages = [];
			foreach ($browser_language_string as $item) {
				list($language, $priority) = explode(';', $item);
				$browser_languages[$priority ?: 'q=1.0'] = $language;
			}
			foreach ($browser_languages as $priority => $browser_language) {
				if (isset($this->languages[$browser_language])) {
					return $browser_language;
				}
			}
		}
		return reset(array_keys($this->languages));
	}

	public function SetLanguage(string $language) {
		if (empty($language)) {
			throw new Exception(T([
				'en' => 'No language specified',
				'ru' => 'Язык не указан',
			]));
		}
		if (!isset($this->languages[$language])) {
			throw new Exception(T([
				'en' => "Language '{$language}' not found",
				'ru' => "Язык '{$language}' не найден",
			]));
		}
		$_SESSION['USER']['LANGUAGE'] = $language;
		if ($this->User->IsAuthorized()) {
			Users::I()->Update($this->User->ID, ['LANGUAGE' => $language]);
		}
	}
}