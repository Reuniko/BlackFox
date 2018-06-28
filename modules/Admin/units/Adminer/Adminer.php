<?php

namespace Admin;

class Adminer extends \System\Unit {

	/** @var \System\SCRUD */
	public $SCRUD;
	public $frame = false;

	public $options = [
		'SCRUD' => [
			'NAME' => 'SCRUD',
		],
	];

	public function __construct() {
		parent::__construct();
		$this->allow_ajax_request = true;
		$this->allow_json_request = true;

		if (isset($_REQUEST['FRAME'])) {
			$this->frame = true;
			$this->ENGINE->WRAPPER = 'frame';
		}
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

	public function GetActions(array $request = []) {
		$actions = [];
		$actions[] = $request['ACTION'];
		$actions[] = (!empty($request['ID']) or isset($request['NEW'])) ? 'Element' : 'Section';
		return $actions;
	}

	public function Section(
		$FILTER = [],
		$PAGE = 1,
		$SORT = ['ID' => 'DESC'],
		$FIELDS = ['*@'],
		$popup = null
	) {
		$this->view = 'section';
		$R['MODE'] = 'SECTION';

		// TODO replace popups with ajax
		if (!empty($popup)) {
			$R['MODE'] = 'POPUP';
			$R['POPUP'] = $popup;
			$this->ENGINE->WRAPPER = 'frame';
		}

		$R['FILTER'] = $FILTER;
		$R['SETTINGS'] = $this->LoadTableSettings();
		$R['STRUCTURE']['FILTERS'] = $this->SCRUD->ExtractStructure($R['SETTINGS']['FILTERS']);
		$R['STRUCTURE']['FIELDS'] = $this->SCRUD->ExtractStructure($R['SETTINGS']['FIELDS']);

		// unset column if frame-mode
		if ($this->frame) {
			unset($R['STRUCTURE']['FIELDS'][$_GET['FRAME']]);
		}

		$R['DATA'] = $this->SCRUD->Search([
			'FILTER' => $FILTER,
			'FIELDS' => $FIELDS,
			'PAGE'   => $PAGE,
			'SORT'   => $SORT,
		]);
		$R['PAGES'] = $this->GetPages(
			$R['DATA']['PAGER']['TOTAL'],
			$R['DATA']['PAGER']['CURRENT'],
			$R['DATA']['PAGER']['LIMIT']
		);
		return $R;
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

	public function GetBackLink() {
		$back = [
			'FILTER' => $_GET['FILTER'],
			'PAGE'   => $_GET['PAGE'],
			'SORT'   => $_GET['SORT'],
			'FRAME'  => $_GET['FRAME']
		];
		$back = array_filter($back, function ($element) {
			return !empty($element);
		});
		$link = '?' . http_build_query($back);
		return $link;
	}

	public function Element($ID = 0, $FILTER = []) {
		$this->view = 'element';

		$R['BACK'] = $this->GetBackLink();
		if ($R['BACK'] <> '?') {
			$this->ENGINE->AddBreadcrumb("...", $R['BACK']);
		}

		if ($ID === 0) {
			$R['MODE'] = 'Create';
			$R['DATA'] = $FILTER + $this->GetDefaultValues();
			$this->ENGINE->AddBreadcrumb("Добавление элемента");
			$R['TABS'] = $this->GetTabsOfCreate();
		} else {
			$R['MODE'] = 'Update';
			$R['DATA'] = $this->SCRUD->Read($ID);
			if (empty($R['DATA'])) {
				throw new Exception("Элемент не найден");
			}
			$this->ENGINE->AddBreadcrumb("Редактирование элемента №{$ID}");
			$R['TABS'] = $this->GetTabsOfUpdate();
		}
		return $R;
	}

	public function Create($FIELDS = [], $REDIRECT = 'Stay') {
		$ID = $this->SCRUD->Create($FIELDS);
		$link = $this->GetLinkForRedirect($ID, $REDIRECT);
		$this->Redirect($link, "Создан элемент <a href='?ID={$ID}'>№{$ID}</a>");
	}

	public function Update($ID, $FIELDS = [], $REDIRECT = 'Stay') {
		$this->SCRUD->Update($ID, $FIELDS);
		$link = $this->GetLinkForRedirect($ID, $REDIRECT);
		$this->Redirect($link, "Обновлен элемент <a href='?ID={$ID}'>№{$ID}</a>");
	}

	private function GetLinkForRedirect($ID, $REDIRECT) {
		$variants = [
			'Stay' => '?' . http_build_query(array_merge($_GET, ['ID' => $ID])),
			'Back' => $this->GetBackLink(),
			'New'  => "?NEW",
		];
		return $variants[$REDIRECT];
	}

	public function Delete($ID) {
		$this->SCRUD->Delete($ID);
		if (is_array($ID)) {
			$message = "Удалены элементы №№ " . implode(', ', $ID);
		} else {
			$message = "Удален элемент №{$ID}";
		}
		$this->Redirect($this->GetBackLink(), $message);
	}

	private function GetPages($total, $current, $limit) {
		$RESULT = [];

		$spread = 7;
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

	public function GetTabsOfCreate() {
		$tabs = [
			'main' => [
				'NAME'   => 'Элемент',
				'VIEW'   => 'element_tab_main',
				'ACTIVE' => true,
			],
		];
		return $tabs;
	}

	public function GetTabsOfUpdate() {
		$tabs = [
			'element' => [
				'NAME'   => 'Элемент',
				'VIEW'   => 'element_tab_main',
				'ACTIVE' => true,
			],
		];
		foreach ($this->SCRUD->structure as $code => $info) {
			if ($info['TYPE'] === 'INNER') {
				$tabs[$code] = [
					'NAME' => $info['NAME'],
					'VIEW' => 'element_tab_external',
				];
			}
		}
		return $tabs;
	}

} 