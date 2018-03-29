<?php

namespace Admin;

class Adminer extends \System\Unit {

	/** @var \System\SCRUD */
	public $SCRUD;

	public $options = [
		'SCRUD' => [
			'NAME' => 'SCRUD',
		],
	];

	public function __construct() {
		parent::__construct();
		$this->allow_ajax_request = true;
		$this->allow_json_request = true;
	}

	public function Init($PARAMS = []) {
		parent::Init($PARAMS);

		if (!is_subclass_of($PARAMS['SCRUD'], 'System\SCRUD')) {
			throw new Exception("Parameter SCRUD ({$PARAMS['SCRUD']}) must be the child of System\\SCRUD");
		}

		if (is_object($PARAMS['SCRUD'])) {
			$this->SCRUD = $PARAMS['SCRUD'];
		} elseif (class_exists($PARAMS['SCRUD'])) {
			$this->SCRUD = $PARAMS['SCRUD']::I();
		}
		$this->ControlUrl();

		$this->ENGINE->TITLE = $this->SCRUD->name;
	}

	public function SelectMethodForView($request = []) {
		if (!empty($request['ID']) or isset($request['NEW'])) {
			return 'Element';
		}
		return 'Section';
	}

	public function SelectMethodForAction($request = array()) {
		if (in_array($request['ACTION'], [
			'Create',
			'Update',
			'Delete',
			'SaveTableSettings',
		])) {
			return $request['ACTION'];
		}
		return null;
	}

	public function Section($FILTER = [], $PAGE = 1, $SORT = ['ID' => 'DESC'], $FIELDS = ['*@'], $popup = null) {
		$this->view = 'section';
		$this->RESULT['MODE'] = 'SECTION';
		if (!empty($popup)) {
			$this->RESULT['MODE'] = 'POPUP';
			$this->RESULT['POPUP'] = $popup;
			$this->ENGINE->WRAPPER = 'frame';
		}
		$this->RESULT['FILTER'] = $FILTER;
		$this->RESULT['SETTINGS'] = $this->LoadTableSettings();
		$this->RESULT['STRUCTURE']['FILTERS'] = $this->SCRUD->ExtractStructure($this->RESULT['SETTINGS']['FILTERS']);
		$this->RESULT['STRUCTURE']['FIELDS'] = $this->SCRUD->ExtractStructure($this->RESULT['SETTINGS']['FIELDS']);
		$this->RESULT['DATA'] = $this->SCRUD->Search([
			'FILTER' => $FILTER,
			'FIELDS' => $FIELDS,
			'PAGE'   => $PAGE,
			'SORT'   => $SORT,
		]);
		$this->RESULT['PAGES'] = $this->GetPages(
			$this->RESULT['DATA']['PAGER']['TOTAL'],
			$this->RESULT['DATA']['PAGER']['CURRENT'],
			$this->RESULT['DATA']['PAGER']['LIMIT']
		);
		return $this->RESULT;
	}

	public function GetDefaultValues() {
		$values = [];
		foreach ($this->SCRUD->structure as $code => $field) {
			if (isset($field['DEFAULT'])) {
				$values[$code] = $field['DEFAULT'];
			}
		}
		return $values;
	}

	public function Element($ID = 0, $FIELDS = []) {
		$this->view = 'element';
		if ($ID === 0) {
			$this->RESULT['MODE'] = 'Create';
			$this->RESULT['DATA'] = $FIELDS + $this->GetDefaultValues();
			$this->ENGINE->AddBreadcrumb("Добавление элемента");
		} else {
			$this->RESULT['MODE'] = 'Update';
			$this->RESULT['DATA'] = $FIELDS + $this->SCRUD->Read($ID);
			if (empty($this->RESULT['DATA'])) {
				throw new Exception("Элемент не найден");
			}
			$this->ENGINE->AddBreadcrumb("Редактирование элемента №{$ID}");
		}
		return $this->RESULT;
	}

	public function Create($FIELDS = [], $REDIRECT = 'Stay') {
		$ID = $this->SCRUD->Create($FIELDS);
		$link = ($REDIRECT === 'Stay') ? "?ID={$ID}" : "?";
		$this->Redirect($link, "Создан элемент №{$ID}");
	}

	public function Update($ID, $FIELDS = [], $REDIRECT = 'Stay') {
		$this->SCRUD->Update($ID, $FIELDS);
		$link = ($REDIRECT === 'Stay') ? "?ID={$ID}" : "?";
		$this->Redirect($link, "Обновлен элемент №{$ID}");
	}

	public function Delete($ID) {
		$this->SCRUD->Delete($ID);
		$this->Redirect('?', "Удален элемент №{$ID}");
	}

	private function GetPages($total, $current, $limit) {
		$RESULT = [];

		$spread = 5;
		$current = $current ?: 1;

		$pages_count = (int)ceil($total / $limit);

		$pages = array_fill(1, $pages_count, null);
		$last_was_excluded = false;
		foreach ($pages as $page => $crap) {
			$in_spread = (
				($page === 1)
				|| ($page === $pages_count)
				|| (abs($page - $current) <= $spread)
			);
			if ($in_spread) {
				$RESULT[$page] = [
					'INDEX'  => $page,
					'ACTIVE' => ($page === $current),
				];
				$last_was_excluded = false;
			} elseif (!$last_was_excluded) {
				$RESULT[$page] = [
					'INDEX' => '...',
					'...'   => true,
				];
				$last_was_excluded = true;
			} else {
				continue;
			}
		}
		return $RESULT;
	}

	public function ControlUrl() {
		if (!empty($_POST)) {
			return;
		}
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && is_array($_GET['FILTER'])) {
			$filter = array();
			foreach ($_GET['FILTER'] as $key => $value) {
				if ($this->SCRUD->_hasInformation($value)) {
					$filter[$key] = $value;
				}
			}
			if (count($filter) < count($_GET['FILTER'])) {
				$_GET['FILTER'] = $filter;
				$url = http_build_query($_GET);
				$url = $this->SanitizeUrl($url);
				$this->Redirect('?' . $url);
			}
		}
		if (strpos($_SERVER['QUERY_STRING'], '%5B') || strpos($_SERVER['QUERY_STRING'], '%5D')) {
			$url = http_build_query($_GET);
			$url = $this->SanitizeUrl($url);
			$this->Redirect('?' . $url);
		}
	}

	public function SanitizeUrl($url) {
		$url = str_replace('%5B', '[', $url);
		$url = str_replace('%5D', ']', $url);
		$url = str_replace('%25', '%', $url);
		$url = str_replace('%3A', ':', $url);
		return $url;
	}

	public function GetDisplayName($row = []) {
		$display = [];
		foreach ($this->SCRUD->structure as $structure_code => $structure_field) {
			if ($structure_field['SHOW']) {
				$display[] = $row[$structure_code];
			}
		}
		$display = implode(' ', $display);
		return $display;
	}

	public function LoadTableSettings() {
		$settings = TableSettings::I()->Read([
			'USER'   => $this->USER->ID,
			'ENTITY' => get_class($this->SCRUD),
		]);
		if (empty($settings)) {
			$settings = [
				'FILTERS' => [$this->SCRUD->key()],
				'FIELDS'  => array_keys($this->SCRUD->structure),
			];
		}
		return $settings;
	}

	public function SaveTableSettings($filters = [], $fields = []) {
		\Admin\TableSettings::I()->Save(
			$this->USER->ID,
			get_class($this->SCRUD),
			$filters,
			$fields
		);
		$this->Redirect(null, 'Настройки сохранены');
	}

}