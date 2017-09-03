<?php

namespace System;

abstract class Component {

	/** @var string Наименование компонента */
	public $name = '...';
	/** @var string Описание компонента */
	public $description = '...';
	/** @var array Ассоциативный массив, описывающий возможные параметры компонента */
	public $options = [];

	/** @var Engine */
	public $ENGINE;
	/** @var ... */
	public $USER;

	/** @var array предки компонента */
	public $components = [];
	/** @var string get_called_class */
	public $class = '...';
	/** @var string шаблон - имя папки шаблона в подпапке /templates/ */
	public $template = 'default';
	/** @var string отображение - имя php файла подключаемого по умолчанию в папке шаблона */
	public $view = 'template';

	/** @var array параметры вызова компонента */
	public $PARAMS = [];
	/** @var array результат работы компонента */
	public $RESULT = [];
	/** @var array сообщения+предупреждения+ошибки возникшие в процессе работы компонента */
	public $MESSAGES = [];

	public $allow_ajax_request = false;
	public $allow_json_request = false;
	public $component_folder;
	public $template_folder;

	public function __construct() {
		// Do not remove local variable $ENGINE, it needs for phpStorm to detect $this->ENGINE as instance of class Engine
		/** @var Engine $ENGINE */
		$ENGINE = Engine::Instance();
		$this->ENGINE = $ENGINE;

		$this->USER = &$_SESSION['USER'];

		// TODO cache all below:

		$this->class = get_called_class();
		list($module, $component) = explode('\\', $this->class);
		$this->components[$this->class] = $this->ENGINE->GetCoreDir("modules/{$module}/components/{$component}");
		$this->component_folder = $this->components[$this->class];

		$parents = class_parents($this);
		foreach ($parents as $parent) {
			if ((new \ReflectionClass($parent))->isAbstract()) {
				continue;
			}
			list($module, $component) = explode('\\', $parent);
			$this->components[$parent] = $this->ENGINE->GetCoreDir("modules/{$module}/components/{$component}");
		}
	}

	public static function Run($PARAMS = [], $template = 'default') {
		$self = new static();
		$self->template = $template ?: $self->template;
		$self->Execute($PARAMS);
	}

	public function Execute($PARAMS = []) {
		$this->template_folder = $this->component_folder . '/templates/' . $this->template;
		$this->Init($PARAMS);
		$this->SetMessagesFromSession();

		if ($this->allow_ajax_request and (in_array($this->class, [$_REQUEST['AJAX'], $_REQUEST['ajax']]))) {
			$this->ENGINE->TEMPLATE = null;
			echo $this->ProcessView($this->ProcessResult());
			return;
		}

		if ($this->allow_json_request and (in_array($this->class, [$_REQUEST['JSON'], $_REQUEST['json']]))) {
			$this->ENGINE->TEMPLATE = null;
			echo json_encode($this->ProcessResult());
			return;
		}

		echo $this->ProcessView($this->ProcessResult());
		return;
	}

	/**
	 * Устанавливает параметры компонента $this->PARAMS:
	 * - проверяет соответствие типов переданных параметров желаемым
	 * - конвертирует типы параметров при несоответствии
	 * - устанавливает параметры по умолчанию
	 * - кидает ошибки в любой непонятной ситуации
	 *
	 * @param array $PARAMS параметры компонента на установку
	 * @throws Exception требуется указать параметр...
	 * @throws Exception передан неизвестный параметр...
	 */
	public function Init($PARAMS = []) {
		$errors = [];
		foreach ($this->options as $code => $option) {
			if (array_key_exists($code, $PARAMS)) {
				$value = $PARAMS[$code];
				switch ($this->options[$code]['TYPE']) {
					case 'STRING':
						$value = (string)$value;
						break;
					case 'BOOLEAN':
						$value = (bool)$value;
						break;
					case 'ARRAY':
						$value = is_array($value) ? $value : [$value];
						break;
				}
				$this->PARAMS[$code] = $value;
				unset($PARAMS[$code]);
				continue;
			}
			if (array_key_exists('DEFAULT', $option)) {
				$this->PARAMS[$code] = $option['DEFAULT'];
				continue;
			}
			$errors[] = "Для инициализации компонента '{$this->class}' требуется указать параметр '{$code}'";
			unset($PARAMS[$code]);
		}
		if (!empty($PARAMS)) {
			foreach ($PARAMS as $code => $value) {
				$errors[] = "В инициализацию компонента '{$this->class}' передан неизвестный параметр '{$code}'";
			}
		}
		if (!empty($errors)) {
			throw new Exception($errors);
		}
	}

	public function Debug($data, $title = '', $mode = 'textarea', $target = '/debug.txt') {
		$this->ENGINE->Debug($data, $title, $mode, $target);
	}

	public function SelectMethodForAction($request = []) {
		return $request['ACTION'] ?: $request['action'];
	}

	public function SelectMethodForView($request) {
		return 'Work';
	}

	public function Work() {
		throw new Exception("Действие 'Work' в компоненте '{$this->class}' не переопределено");
	}

	/**
	 * Selects the necessary methods (for action, for view)
	 * and returns the combined result of their launch:
	 * 1) Invoke method for action - catch any exceptions, add them to result if necessary
	 * 2) Invoke method for view - return result
	 *
	 * @param array $request user request
	 * @return array result data
	 * @throws Exception
	 */
	public function Controller($request = []) {
		$error_result = [];

		$action = $this->SelectMethodForAction($request);
		if (!empty($action)) {
			try {
				return (array)$this->Invoke($action, $request);
			} catch (\Exception $error) {
				$error_result = (array)$this->Error($error->getMessage(), $action, $request);
			}
		}

		$action = $this->SelectMethodForView($request);
		if (!empty($action)) {
			return (array)$this->Invoke($action, $request) + $error_result;
		}

		throw new Exception("Не удалось определить требуемое действие");
	}

	/**
	 * Вызывает действие, контролируя его наличие и публичный доступ к нему.
	 * Действие запускается через ReflectionMethod->invokeArgs() что позволяет самому действию
	 * контролировать принимаемые на вход параметры. Все прочие параметры отсеиваются.
	 *
	 * @param string $action действие - название публичного метода в классе контроллера
	 * @param array $request данные запроса - ассоциативный массив данных пришедших по любому каналу
	 * @return array результат выполнения действия (зачастую действие самостоятельно редиректит дальше)
	 * @throws Exception
	 */
	private function Invoke($action, $request) {
		if (!method_exists($this, $action)) {
			throw new Exception("Неизвестное действие - '{$action}'");
		}
		//$this->Debug($action, 'Invoke $action');
		//$this->Debug($request, 'Invoke $request');
		$reflection = new \ReflectionMethod($this, $action);
		if (!$reflection->isPublic()) {
			throw new Exception("Действие '{$action}' запрещено вызывать из контроллера публичной части");
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
	 * @param string $message
	 * @param string $action
	 * @param array $request
	 * @return array
	 */
	public function Error($message, $action = null, $request = []) {
		//$this->Debug($message, 'Error $error');
		//$this->Debug($action, 'Error $action');
		//$this->Debug($request, 'Error $request');
		$this->MESSAGES[] = [
			'TYPE'    => 'ERROR',
			'MESSAGE' => $message,
		];
		return [];
	}

	/**
	 *
	 */
	public function SetMessagesFromSession() {
		if (!empty($_SESSION['MESSAGES'][$this->class])) {
			$this->MESSAGES = $_SESSION['MESSAGES'][$this->class];
			$_SESSION['MESSAGES'][$this->class] = [];
		}
	}

	/**
	 * Returns the result of the execution of the controller,
	 * passing a request combined from globals
	 *
	 * @return array result data
	 */
	public function ProcessResult() {
		$request = array_merge_recursive($_REQUEST, $this->_files());
		$this->RESULT = (array)$this->Controller($request);
		return $this->RESULT;
	}

	/**
	 * Connects the [$RESULT] to a [$this->template]
	 * and returns the resulting content
	 *
	 * @param array $RESULT result data
	 * @return null|string content (html)
	 */
	public function ProcessView($RESULT) {
		if (empty($this->view)) {
			return null;
		}

		$view_file = $this->Path("{$this->view}.php");

		ob_start();
		require($view_file);
		$content = ob_get_clean();

		if (!empty($this->MESSAGES)) {
			ob_start();
			$this->ShowMessages();
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
	 * @throws Exception Template not found...
	 */
	public function Path($path) {
		foreach ($this->components as $component => $component_folder) {
			$search = "{$component_folder}/templates/{$this->template}/{$path}";
			if (file_exists($search)) {
				return $search;
			}
		}
		throw new Exception("Template not found: '{$path}'");
	}

	public function TemplateParentPath($path = null) {
		$template_file = debug_backtrace()[0]['file'];
		$template_file = str_replace('\\', '/', $template_file);
		$template_folder = str_replace('\\', '/', $this->template_folder);
		$relative_template_file = str_replace($template_folder, '', $template_file);
		debug($this->components, '$this->components');
		foreach ($this->components as $object => $path) {
			if ($this->class === $object) {
				continue;
			}
			$search = "{$path}/templates/{$this->template}{$relative_template_file}";
			if (file_exists($search)) {
				return $search;
			}
		}
		throw new Exception("Template parent path not found");
	}

	public function ShowMessages() {
		$types = [
			'SUCCESS' => 'success',
			'INFO'    => 'info',
			'WARNING' => 'warning',
			'ERROR'   => 'danger',
			'DANGER'  => 'danger',
		];
		foreach ($this->MESSAGES as $message) {
			echo "<div class='alert alert-{$types[$message['TYPE']]}'>{$message['MESSAGE']}</div>";
		}
		$this->MESSAGES = [];
	}

	/**
	 * Корректирует неадекватную структуру суперглобального массива $_FILES.
	 * Возвращает строго упорядоченный на два уровня массив пришедших файлов.
	 * Файлы, пришедшие со статусом ошибки №4 (файл не приложен) отсеиваются.
	 *
	 * @global array $_FILES
	 * @return array пришедшие файлы
	 * @todo упорядочивать на неограниченную вложенность
	 */
	private function _files() {
		$files = [];
		foreach ($_FILES as $code1 => $file) {
			if (!is_array($file['name'])) {
				$files[$code1] = $file;
			} else {
				foreach ($_FILES[$code1]['name'] as $code2 => $crap) {
					if (empty($_FILES[$code1]['error'][$code2])) {
						$files[$code1][$code2] = [
							'name'     => $_FILES[$code1]['name'][$code2],
							'type'     => $_FILES[$code1]['type'][$code2],
							'tmp_name' => $_FILES[$code1]['tmp_name'][$code2],
							'error'    => $_FILES[$code1]['error'][$code2],
							'size'     => $_FILES[$code1]['size'][$code2],
						];
					}
				}
			}
		}
		return $files;
	}

	/**
	 * Перенаправляет на другой URL.
	 * Добавляет в заголовки "Message" если он указан.
	 * Завершает выполнение скрипта.
	 *
	 * @param string|null $url URL-адрес
	 * @param string $message_text текстовое сообщение
	 * @param string $message_type тип текстового сообщения (SUCCESS|WARNING|ERROR)
	 * @throws Exception
	 */
	public function Redirect($url, $message_text = null, $message_type = 'SUCCESS') {
		header('Location: ' . ($url ?: $_SERVER['REQUEST_URI']));
		if (!empty($message_text)) {
			$_SESSION['MESSAGES'][$this->class][] = [
				'TYPE'    => $message_type,
				'MESSAGE' => $message_text,
			];
		}
		echo json_encode([
			'URL'     => $url,
			'TYPE'    => $message_type,
			'MESSAGE' => $message_text,
		]);
		die();
	}

}
