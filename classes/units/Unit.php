<?php

namespace BlackFox;

abstract class Unit {

	use Instance;

	/** @var string Наименование */
	public $name;
	/** @var string Описание */
	public $description;
	/** @var array Ассоциативный массив, описывающий возможные параметры */
	public $options = [];

	/** @var Engine $ENGINE */
	public $ENGINE;
	/** @var User $USER */
	public $USER;

	/** @var array предки компонента */
	public $parents = [];
	/** @var Unit get_called_class */
	public $class = '...';
	/** @var string отображение - имя php файла подключаемого в подпапке /views */
	public $view = null;

	/** @var array запрос пользователя */
	public $REQUEST = [];
	/** @var array параметры вызова */
	public $PARAMS = [];
	/** @var array результат работы */
	public $RESULT = [];
	/**
	 * - TYPE - часть CSS-класса alert-*
	 * - TEXT - контент алерта
	 * @var array сообщения+предупреждения+ошибки возникшие в процессе работы:
	 */
	public $ALERTS = [];

	public $ajax = false;
	public $json = false;
	public $allow_ajax_request = false;
	public $allow_json_request = false;

	/** @var string абсолютный путь к папке юнита */
	public $unit_absolute_folder;
	/** @var string относительный путь к папке юнита */
	public $unit_relative_folder;
	/** @var string абсолютный путь к папке с отображениями */
	public $view_absolute_folder;
	/** @var string относительный путь к папке с отображениями */
	public $view_relative_folder;

	public function __construct(
		Engine $Engine = null,
		User $User = null
	) {
		$this->ENGINE = $Engine ?: Engine::I();
		$this->USER = $User ?: User::I();

		$this->class = get_called_class();
		$this->name = $this->name ?: $this->class;
		$this->parents[$this->class] = dirname($this->ENGINE->classes[$this->class]);

		$this->unit_absolute_folder = $this->parents[$this->class];
		$this->unit_relative_folder = $this->ENGINE->GetRelativePath($this->unit_absolute_folder);

		$this->view_absolute_folder = $this->unit_absolute_folder . '/views';
		$this->view_relative_folder = $this->unit_relative_folder . '/views';

		// collect info about all parents including self, excluding abstract classes
		$parents = class_parents($this);
		foreach ($parents as $parent) {
			if ((new \ReflectionClass($parent))->isAbstract()) {
				continue;
			}
			$this->parents[$parent] = dirname($this->ENGINE->classes[$parent]);
		}
	}

	public static function Run($params = [], $request = []) {
		/**@var Unit $class */
		$class = get_called_class();
		/**@var Unit $Unit */
		$Unit = $class::I();
		try {
			$Unit->Execute($params, $request);
		} catch (Exception $error) {
			$Unit->ENGINE->ShowErrors($error->getArray());
		} catch (\Exception $error) {
			$Unit->ENGINE->ShowErrors([$error->getMessage()]);
		}
	}

	/**
	 * @param array $params
	 * @param array $request
	 * @throws Exception
	 */
	public function Execute($params = [], $request = []) {
		$this->Init($params);
		$this->SetAlertsFromSession();

		$this->ajax = $this->IsRequestTypeAjax();
		$this->json = $this->IsRequestTypeJson();

		$this->REQUEST = $this->GetRequest($request);
		$this->RESULT = $this->Controller($this->REQUEST);

		if ($this->ajax) {
			$this->ENGINE->TEMPLATE = null;
			echo $this->ProcessView($this->RESULT);
			return;
		}

		if ($this->json) {
			$this->ENGINE->TEMPLATE = null;
			echo json_encode($this->RESULT + ['ALERTS' => $this->ALERTS]);
			return;
		}

		$this->ManageHeaders();
		echo $this->ProcessView($this->RESULT);
		return;
	}

	public function IsRequestTypeAjax() {
		return $this->allow_ajax_request and in_array($this->class, [$_REQUEST['AJAX'], $_REQUEST['ajax']]);
	}

	public function IsRequestTypeJson() {
		return $this->allow_json_request and in_array($this->class, [$_REQUEST['JSON'], $_REQUEST['json']]);
	}

	/**
	 * Устанавливает параметры компонента $this->PARAMS:
	 * - проверяет соответствие типов переданных параметров желаемым
	 * - устанавливает параметры по умолчанию
	 * - кидает ошибки в любой непонятной ситуации
	 *
	 * @param array $params параметры компонента на установку
	 * @throws Exception требуется указать параметр...
	 */
	public function Init($params = []) {
		$errors = [];
		foreach ($this->options as $code => $option) {
			if (isset($params[$code]) || array_key_exists($code, $params)) {
				$value = $params[$code];

				if (!empty($this->options[$code]['TYPE'])) {
					$type_expected = strtolower($this->options[$code]['TYPE']); // strtolower - for backward compatibility
					$type_passed = gettype($value);
					if ($type_expected <> $type_passed) {
						$errors[] = "Unit '{$this->class}' initialisation error: param - '{$code}', expecting type - '{$type_expected}', passed value type - '{$type_passed}'";
						unset($params[$code]);
						continue;
					}
				}

				$this->PARAMS[$code] = $value;
				unset($params[$code]);
				continue;
			}
			if (isset($option['DEFAULT']) || array_key_exists('DEFAULT', $option)) {
				$this->PARAMS[$code] = $option['DEFAULT'];
				continue;
			}
			$errors[] = "Unit '{$this->class}' initialisation error: required param - '{$code}'";
			unset($params[$code]);
		}
		if (!empty($errors)) {
			throw new Exception($errors);
		}
		if (!empty($params)) {
			foreach ($params as $code => $value) {
				$this->PARAMS[$code] = $value;
			}
		}
	}

	public function Debug($data, $title = '', $mode = 'print_r', $target = '/debug.txt') {
		if (function_exists('debug')) {
			debug($data, $title, $mode, $target);
		}
	}

	/**
	 * Analyzes the $request, matches the required methods for actions.
	 * May setup required view ($this->view).
	 *
	 * @param array $request user input
	 * @return string|array names of methods
	 */
	public function GetActions(array $request = []) {
		$actions = [];

		$request['ACTION'] = $request['ACTION'] ?: $request['action'] ?: null;
		$request['VIEW'] = $request['VIEW'] ?: $request['view'] ?: null;

		if ($request['ACTION']) {
			$actions[] = $request['ACTION'];
		}

		if ($request['VIEW']) {
			$actions[] = $request['VIEW'];
		} else {
			$actions[] = 'Default';
		}

		return $actions;
	}

	/**
	 * Контролирует поток управления юнита:
	 * - запрашивает массив действий ($this->GetActions)
	 * - разбивает массив действий на две части: все действия кроме финального + финальное действие
	 * - инициализирует $this->view именем финального действия
	 * - последовательно выполняет все действия кроме финального:
	 *    успех -- агрегирует ответ: массив - в результат, строку - в успешные сообщения;
	 *    ошибка -- ловит ошибку и отправляет ее в $this->Error;
	 *    редирект -- прекращает выполнение.
	 * - выполняет финальное действие:
	 *    успех -- преобразует ответ в массив и агрегирует его в результат;
	 *    ошибка -- не ловит ошибку, позволяя ей всплыть на уровень выше;
	 *    редирект -- прекращает выполнение.
	 *
	 * Все действия кроме финального:
	 * - могут пытаться изменить внутреннее состояние модели
	 * - могут подготавливать дополнительные данные для отображения
	 *
	 * Финальное действие:
	 * - не пытается изменить внутреннее состояние модели
	 * - подготавливает основные данные для отображения
	 *
	 * @param array $request user request
	 * @return array result data
	 * @throws Exception
	 */
	public function Controller(array $request = []) {

		$actions = (array)$this->GetActions($request);
		$actions = array_filter($actions, 'strlen');
		if (empty($actions)) {
			throw new Exception("Unit '{$this->name}', controller can't find any actions");
		}
		$final_action = array_pop($actions);
		$this->view = $this->view ?: $final_action;
		$result = [];

		foreach ($actions as $action) {
			try {
				$answer = $this->Invoke($action, $request);
				if (is_array($answer)) {
					$result += $answer;
					continue;
				}
				if (is_string($answer)) {
					$this->ALERTS[] = ['TYPE' => 'success', 'TEXT' => $answer];
					continue;
				}
				throw new Exception("Unknown answer type: '" . gettype($answer) . "' for action '{$action}'");
			} catch (Exception $Exception) {
				$result += $this->Error($Exception, $action, $request);
			}
		}

		$result += (array)$this->Invoke($final_action, $request);
		return $result;
	}

	/**
	 * Вызывает метод, контролируя его наличие и публичный доступ к нему.
	 * Метод запускается через ReflectionMethod->invokeArgs() что позволяет самому методу
	 * контролировать принимаемые на вход параметры. Все прочие параметры отсеиваются.
	 *
	 * @param string $method метод - название публичного метода в классе контроллера
	 * @param array $request данные запроса - ассоциативный массив данных пришедших по любому каналу
	 * @return array|string результат выполнения метода (зачастую метод самостоятельно редиректит дальше)
	 * @throws \Exception
	 */
	private function Invoke($method, $request) {
		if (!method_exists($this, $method)) {
			throw new Exception("Unit '{$this->name}', unknown method - '{$method}'");
		}
		//$this->Debug($action, 'Invoke $action');
		//$this->Debug($request, 'Invoke $request');
		$reflection = new \ReflectionMethod($this, $method);
		if (!$reflection->isPublic()) {
			throw new Exception("Unit '{$this->name}', method '{$method}' is not public");
		}

		$request = array_change_key_case($request);
		$parameters = $reflection->getParameters();
		//$this->Debug($parameters, 'Invoke $parameters');
		$arguments = [];
		foreach ($parameters as $parameter) {
			$code = strtolower($parameter->name);
			if (isset($request[$code])) {
				$arguments[$code] = $request[$code];
			} else {
				try {
					$arguments[$code] = $parameter->getDefaultValue();
				} catch (\Exception $error) {
					$arguments[$code] = null;
				}
			}
		}

		//$this->Debug($arguments, 'Invoke $arguments');
		return $reflection->invokeArgs($this, $arguments);
	}

	/**
	 * Вызывается при ловле ошибки выполнения любого действия из под контроллера.
	 * Может переопределяться в классах-наследниках для переопределения логики обработки ошибок.
	 *
	 * @param Exception $Exception
	 * @param string $action
	 * @param array $request
	 * @return array
	 */
	public function Error(Exception $Exception, $action = null, $request = []) {
		$this->Debug($Exception, '$Exception');
		foreach ($Exception->getArray() as $error) {
			$this->ALERTS[] = [
				'TYPE' => 'danger',
				'TEXT' => $error,
			];
		}
		return [];
	}

	public function SetAlertsFromSession() {
		if (!empty($_SESSION['ALERTS'][$this->class])) {
			$this->ALERTS = $_SESSION['ALERTS'][$this->class];
			$_SESSION['ALERTS'][$this->class] = [];
		}
	}

	public function GetRequest($request = []) {
		return array_merge_recursive($request, $_REQUEST, $this->ConvertFilesStructure($_FILES));
	}

	/**
	 * Connects the [$RESULT] to a [$this->view]
	 * and returns the resulting content
	 *
	 * @param array $RESULT result data
	 * @return null|string content (html)
	 * @throws Exception
	 */
	public function ProcessView($RESULT) {
		if (empty($this->view)) {
			return null;
		}

		ob_start();
		$this->Debug([
			'PARAMS' => $this->PARAMS,
			'ALERTS' => $this->ALERTS,
			'RESULT' => $RESULT,
		], $this->class);
		$view_file = $this->Path("{$this->view}.php");
		require($view_file);
		$content = ob_get_clean();

		if (!empty($this->ALERTS)) {
			ob_start();
			$this->ShowAlerts();
			$content = ob_get_clean() . $content;
		}

		return $content;
	}

	/**
	 * Возвращает абсолютный путь к шаблону:
	 * - ищет возможный шаблон в текущем компоненте
	 * - ищет возможный шаблон во всех компонентах-родителях
	 * - возвращает ошибку если подходящий шаблон не найден
	 *
	 * @param string $path путь к шаблону относительно корневой директории шаблона
	 * @return string абсолютный путь к шаблону
	 * @throws Exception View not found...
	 */
	public function Path($path) {
		foreach ($this->parents as $unit => $unit_folder) {
			$search = "{$unit_folder}/views/{$path}";
			if (file_exists($search)) {
				return $search;
			}
		}
		throw new Exception("Unit '{$this->class}'; View not found: '{$path}'");
	}

	public function PathInclude($path) {
		try {
			return $this->Path($path);
		} catch (Exception $error) {
			return null;
		}
	}

	public function GetParentView() {
		$view_file = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0]['file'];
		$view_file = str_replace('\\', '/', $view_file);
		$view_folder = str_replace('\\', '/', $this->view_absolute_folder);
		$view_relative_path = str_replace($view_folder, '', $view_file);
		//debug($this->units, '$this->units');
		foreach ($this->parents as $unit => $unit_folder) {
			if ($this->class === $unit) {
				continue;
			}
			$search = "{$unit_folder}/views{$view_relative_path}";
			if (file_exists($search)) {
				return $search;
			}
		}
		throw new Exception("Parent's view not found");
	}

	public function ShowAlerts() {
		foreach ($this->ALERTS as $message) {
			echo "<div class='alert alert-{$message['TYPE']}'>{$message['TEXT']}</div>";
		}
		$this->ALERTS = [];
	}

	/**
	 * Корректирует неадекватную структуру суперглобального массива $_FILES.
	 * Возвращает рекурсивно упорядоченный массив пришедших файлов.
	 *
	 * @param array $input ожидается массив $_FILES
	 * @return array массив со скорректированной структурой
	 */
	private function ConvertFilesStructure($input) {
		$output = [];
		foreach ($input as $key => $file) {
			$output[$key] = $this->ConvertFilesStructureRecursive(
				$file['name'],
				$file['type'],
				$file['tmp_name'],
				$file['error'],
				$file['size']
			);
		}
		return $output;
	}

	private function ConvertFilesStructureRecursive($name, $type, $tmp_name, $error, $size) {
		if (!is_array($name)) {
			return [
				'name'     => $name,
				'type'     => $type,
				'tmp_name' => $tmp_name,
				'error'    => $error,
				'size'     => $size,
			];
		}
		$output = [];
		foreach ($name as $key => $_crap) {
			$output[$key] = $this->ConvertFilesStructureRecursive(
				$name[$key],
				$type[$key],
				$tmp_name[$key],
				$error[$key],
				$size[$key]
			);
		}
		return $output;
	}

	/**
	 * Добавляет в сессию уведомления (если они указаны).
	 * Перенаправляет на другой URL.
	 * Завершает выполнение скрипта.
	 *
	 * @param string|null $url URL-адрес (null = $_SERVER['REQUEST_URI'])
	 * @param string|array $alerts строка, уведомление об успехе
	 * | массив строк, уведомлений об успехе
	 * | массив массивов, представляющих собой уведомления в формате [TEXT => '...', TYPE => (success|info|warning|danger)]
	 */
	public function Redirect($url, $alerts = []) {
		$url = is_null($url) ? $_SERVER['REQUEST_URI'] : $url;
		$alerts = is_array($alerts) ? $alerts : [$alerts];
		foreach ($alerts as &$alert) {
			if (is_string($alert)) {
				$alert = ['TEXT' => $alert, 'TYPE' => 'success'];
			}
		}
		if ($this->json) {
			echo json_encode([
				'URL'    => $url,
				'ALERTS' => $alerts,
			]);
		} else {
			header('Location: ' . ($url));
			$_SESSION['ALERTS'][$this->class] = $alerts;
		}
		die();
	}

	/**
	 * Добавляет в заголовки страницы файлы 'style.css' и 'script.js' если они существуют
	 */
	public function ManageHeaders() {
		// style.css
		try {
			$style_absolute_path = $this->Path('style.css');
			$style_relative_path = $this->ENGINE->GetRelativePath($style_absolute_path);
			$this->ENGINE->AddHeaderStyle($style_relative_path);
		} catch (Exception $error) {
		}
		// script.js
		try {
			$script_absolute_path = $this->Path('script.js');
			$script_relative_path = $this->ENGINE->GetRelativePath($script_absolute_path);
			$this->ENGINE->AddHeaderScript($script_relative_path);
		} catch (Exception $error) {
		}
	}

}
