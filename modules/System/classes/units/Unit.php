<?php

namespace System;

abstract class Unit {

	/** @var string Наименование */
	public $name;
	/** @var string Описание */
	public $description;
	/** @var array Ассоциативный массив, описывающий возможные параметры */
	public $options = [];

	/** @var Engine Engine */
	public $ENGINE;
	/** @var array User */
	public $USER;

	/** @var array предки компонента */
	public $parents = [];
	/** @var string get_called_class */
	public $class = '...';
	/** @var string шаблон - имя папки шаблона в подпапке /templates/ */
	public $template = 'default';
	/** @var string отображение - имя php файла подключаемого в папке шаблона */
	public $view = 'default';

	/** @var array параметры вызова */
	public $PARAMS = [];
	/** @var array результат работы */
	public $RESULT = [];
	/** @var array сообщения+предупреждения+ошибки возникшие в процессе работы */
	public $MESSAGES = [];

	public $allow_ajax_request = false;
	public $allow_json_request = false;

	/** @var string абсолютный путь к папке юнита */
	public $unit_absolute_folder;
	/** @var string относительный путь к папке юнита */
	public $unit_relative_folder;
	/** @var string абсолютный путь к папке шаблона */
	public $template_absolute_folder;
	/** @var string относительный путь к папке шаблона */
	public $template_relative_folder;

	public function __construct($template = 'default') {
		// Do not remove local variable $ENGINE, it needs for phpStorm to detect $this->ENGINE as instance of class
		/** @var Engine $ENGINE */
		$ENGINE = Engine::Instance();
		$this->ENGINE = $ENGINE;

		// Do not remove local variable $USER, it needs for phpStorm to detect $this->USER as instance of class
		/** @var User $USER */
		$USER = User::I();
		$this->USER = $USER;

		// TODO cache all below:

		$this->class = get_called_class();
		$this->name = $this->name ?: $this->class;
		list($module, $unit) = explode('\\', $this->class);
		$this->parents[$this->class] = $this->ENGINE->GetCoreDir("modules/{$module}/units/{$unit}");
		$this->unit_absolute_folder = $this->parents[$this->class];
		$this->unit_relative_folder = $this->ENGINE->GetRelativePath($this->unit_absolute_folder);

		// collect info about all parents including self, excluding abstract classes
		$parents = class_parents($this);
		foreach ($parents as $parent) {
			if ((new \ReflectionClass($parent))->isAbstract()) {
				continue;
			}
			list($module, $unit) = explode('\\', $parent);
			$this->parents[$parent] = $this->ENGINE->GetCoreDir("modules/{$module}/units/{$unit}");
		}

		$this->template = $this->SelectTemplateFolder($template);
		$this->template_absolute_folder = $this->unit_absolute_folder . '/templates/' . $this->template;
		$this->template_relative_folder = $this->ENGINE->GetRelativePath($this->template_absolute_folder);
	}

	/**
	 * Метод должен вычислить и вернуть путь <template> к папке шаблона относительно папки templates:
	 * /<core|site>/modules/<module>/units/<unit>/templates/<template>
	 *
	 * @param string $template
	 * @return string
	 */
	public function SelectTemplateFolder($template) {
		return $template;
	}

	public static function Run($PARAMS = [], $template = 'default') {
		$self = new static($template);
		try {
			$self->Execute($PARAMS);
		} catch (Exception $error) {
			Engine::I()->ShowErrors([$error->getMessage()]);
		}
	}

	public function Execute($PARAMS = []) {
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

		$this->ManageHeaders();
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
					case 'OBJECT':
						if (!is_object($value)) {
							throw new Exception("Unit param '{$code}' expect object");
						}
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

	/**
	 * Analyzes the $request, matches the required methods for actions.
	 * May setup required view ($this->view).
	 *
	 * @param array $request user input
	 * @return string|array names of methods
	 */
	public function GetActions(array $request = []) {
		$actions = [];
		if ($request['ACTION'] or $request['action']) {
			$actions[] = $request['ACTION'] ?: $request['action'];
		}
		if ($request['VIEW'] or $request['view']) {
			$actions[] = $request['VIEW'] ?: $request['view'];
			$this->view = $request['VIEW'] ?: $request['view'];
			return $actions;
		}
		$actions[] = 'Work';
		return $actions;
	}

	/**
	 * Контролирует поток управления юнита:
	 * - запрашивает массив действий ($this->GetActions)
	 * - разбивает массив действий на две части: все действия кроме финального + финальное действие
	 * - последовательно выполняет все действия кроме финального:
	 *    - успех - агрегирует ответ: массив - в результат, строку - в успешные сообщения
	 *    - ошибка - ловит ошибку и отправляет ее в $this->Error
	 *    - редирект - прекращает выполнение
	 * - выполняет финальное действие:
	 *    - успех - агрегирует ответ в результат
	 *    - ошибка - не ловит ошибку, позволяя ей всплыть на уровень выше
	 *    - редирект - прекращает выполнение
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
		$result = [];

		foreach ($actions as $action) {
			try {
				$answer = $this->Invoke($action, $request);
				if (is_array($answer)) {
					$result += $answer;
				} else {
					$this->MESSAGES[] = ['TYPE' => 'SUCCESS', 'TEXT' => $answer];
				}
			} catch (Exception $error) {
				$result += $this->Error($error->getMessage(), $action, $request);
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
	 * @throws Exception
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
			'TYPE' => 'ERROR',
			'TEXT' => $message,
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

		ob_start();
		debug($RESULT, $this->class . ' $RESULT');
		$view_file = $this->Path("{$this->view}.php");
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
	 * @throws Exception View not found...
	 */
	public function Path($path) {
		foreach ($this->parents as $unit => $unit_folder) {
			$search = "{$unit_folder}/templates/{$this->template}/{$path}";
			if (file_exists($search)) {
				return $search;
			}
		}
		throw new Exception("View not found: '{$path}'");
	}

	public function PathInclude($path) {
		try {
			return $this->Path($path);
		} catch (Exception $error) {
			return null;
		}
	}

	public function TemplateParentPath() {
		$template_file = debug_backtrace()[0]['file'];
		$template_file = str_replace('\\', '/', $template_file);
		$template_folder = str_replace('\\', '/', $this->template_absolute_folder);
		$relative_template_file = str_replace($template_folder, '', $template_file);
		//debug($this->units, '$this->units');
		foreach ($this->parents as $object => $path) {
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
			echo "<div class='alert alert-{$types[$message['TYPE']]}'>{$message['TEXT']}</div>";
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
				'TYPE' => $message_type,
				'TEXT' => $message_text,
			];
		}
		echo json_encode([
			'URL'  => $url,
			'TYPE' => $message_type,
			'TEXT' => $message_text,
		]);
		die();
	}

	public function ManageHeaders() {
		try {
			$style = $this->ENGINE->GetRelativePath($this->Path('style.css'));
			$this->ENGINE->AddHeaderStyle($style);
		} catch (\Exception $error) {
		}
		try {
			$style = $this->ENGINE->GetRelativePath($this->Path('script.js'));
			$this->ENGINE->AddHeaderScript($style);
		} catch (\Exception $error) {
		}
	}

}
