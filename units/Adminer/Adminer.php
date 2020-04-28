<?php

namespace BlackFox;

class Adminer extends \BlackFox\Unit {

	/** @var \BlackFox\SCRUD */
	public $SCRUD;
	public $frame = false;

	public $options = [
		'SCRUD'        => [
			'NAME' => 'SCRUD',
		],
		'RESTRICTIONS' => [
			'TYPE'    => 'array',
			'NAME'    => 'RESTRICTIONS',
			'DEFAULT' => [],
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

		if (!is_subclass_of($PARAMS['SCRUD'], 'BlackFox\SCRUD')) {
			throw new Exception("Parameter SCRUD ({$PARAMS['SCRUD']}) must be the child of BlackFox\\SCRUD");
		}

		if (is_object($PARAMS['SCRUD'])) {
			$this->SCRUD = $PARAMS['SCRUD'];
		} elseif (class_exists($PARAMS['SCRUD'])) {
			$this->SCRUD = $PARAMS['SCRUD']::I();
		}
		$this->ControlUrl();

		$this->ENGINE->TITLE = $this->SCRUD->name;

		$back_link = $this->GetBackLink();
		if ($back_link <> '?') {
			$this->ENGINE->AddBreadcrumb("...", $back_link);
		}
	}

	public function GetActions(array $request = []) {
		if ($request['ACTION'] === 'SearchOuter') {
			return ['SearchOuter'];
		}

		$actions = $request['ACTION'] ? [$request['ACTION']] : [];

		if (isset($request['NEW'])) {
			return array_merge($actions, ['CreateForm']);
		}

		if (!empty($request['ID'])) {
			return array_merge($actions, ['UpdateForm']);
		}

		return array_merge($actions, ['Section']);
	}

	public function Section(
		$FILTER = [],
		$PAGE = 1,
		$SORT = ['ID' => 'DESC'],
		$FIELDS = ['*@@']
	) {
		$R['MODE'] = 'SECTION';

		$FILTER = $this->PARAMS['RESTRICTIONS'] + $FILTER;

		$R['FILTER'] = $FILTER;
		$R['SORT'] = $SORT;
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
		debug($this->SCRUD->SQL, 'SQL');
		return $R;
	}

	public function GetDefaultValues() {
		$values = [];
		foreach ($this->SCRUD->fields as $code => $field) {
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
			'FRAME'  => $_GET['FRAME'],
		];
		$back = array_filter($back, function ($element) {
			return !empty($element);
		});
		$link = '?' . http_build_query($back);
		return $link;
	}

	public function CreateForm($FILTER = [], $FIELDS = []) {
		$R['MODE'] = 'Create';

		$R['DATA'] = $this->PARAMS['RESTRICTIONS'] + $FILTER + $this->GetDefaultValues() + $FIELDS;

		$R['BACK'] = $this->GetBackLink();
		$R['TABS'] = $this->GetTabsOfCreate();

		foreach ($this->PARAMS['RESTRICTIONS'] as $code => $value) {
			$this->SCRUD->fields[$code]['DISABLED'] = true;
		}

		$this->ENGINE->AddBreadcrumb(T([
			'en' => 'Add element',
			'ru' => 'Добавление элемента',
		]));
		$this->view = 'element';
		return $R;
	}

	public function UpdateForm($ID = null, $FIELDS = []) {
		$R['MODE'] = 'Update';

		$R['DATA'] = $this->SCRUD->Read($this->PARAMS['RESTRICTIONS'] + ['ID' => $ID], ['*@@']);
		if (empty($R['DATA'])) {
			throw new Exception(T([
				'en' => 'Element not found',
				'ru' => 'Элемент не найден',
			]));
		}
		$R['DATA'] = $FIELDS + $R['DATA'];

		$R['BACK'] = $this->GetBackLink();
		$R['TABS'] = $this->GetTabsOfUpdate();

		foreach ($this->PARAMS['RESTRICTIONS'] as $code => $value) {
			$this->SCRUD->fields[$code]['DISABLED'] = true;
		}

		$this->ENGINE->AddBreadcrumb(T([
			'en' => "Edit element #{$ID}",
			'ru' => "Редактирование элемента №{$ID}",
		]));
		$this->view = 'element';
		return $R;
	}


	public function Create($FIELDS = [], $REDIRECT = 'Stay') {
		foreach ($this->PARAMS['RESTRICTIONS'] as $code => $value) {
			$FIELDS[$code] = $value;
		}
		$ID = $this->SCRUD->Create($FIELDS);
		$link = $this->GetLinkForRedirect($ID, $REDIRECT);
		$this->Redirect($link, T([
			'en' => "Element <a href='?ID={$ID}'>#{$ID}</a> has been created",
			'ru' => "Создан элемент <a href='?ID={$ID}'>№{$ID}</a>",
		]));
	}

	public function Update($ID, $FIELDS = [], $REDIRECT = 'Stay') {
		foreach ($this->PARAMS['RESTRICTIONS'] as $code => $value) {
			unset($FIELDS[$code]);
		}
		$this->SCRUD->Update($ID, $FIELDS);
		$link = $this->GetLinkForRedirect($ID, $REDIRECT);
		$this->Redirect($link, T([
			'en' => "Element <a href='?ID={$ID}'>#{$ID}</a> has been updated",
			'ru' => "Обновлен элемент <a href='?ID={$ID}'>№{$ID}</a>",
		]));
	}

	private function GetLinkForRedirect($ID, $REDIRECT) {
		$get = $_GET;
		unset($get['NEW']);
		$variants = [
			'Stay' => '?' . http_build_query(array_merge($get, ['ID' => $ID])),
			'Back' => $this->GetBackLink(),
			'New'  => "?NEW",
		];
		return $variants[$REDIRECT];
	}

	public function Delete($ID) {
		$this->SCRUD->Delete($ID);
		if (is_array($ID)) {
			$message = T([
				'en' => "Elements ## " . implode(', ', $ID) . " has been deleted",
				'ru' => "Удалены элементы №№ " . implode(', ', $ID),
			]);
		} else {
			$message = T([
				'en' => "Element #{$ID} has been deleted",
				'ru' => "Удален элемент №{$ID}",
			]);
		}
		$this->Redirect($this->GetBackLink(), $message);
	}

	public function ControlUrl() {
		if (!empty($_POST)) {
			return;
		}
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && is_array($_GET['FILTER'])) {
			$filter = [];
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

	public function LoadTableSettings() {
		$settings = TableSettings::I()->Read([
			'USER'   => $this->USER->ID,
			'ENTITY' => get_class($this->SCRUD),
		]);
		if (empty($settings)) {
			$settings = [];
			foreach ($this->SCRUD->fields as $code => $field) {
				if ($field['VITAL'] or $field['PRIMARY']) {
					$settings['FILTERS'][] = $code;
				}
				if (!$field['DISABLED'] or $field['PRIMARY']) {
					$settings['FIELDS'][] = $code;
				}
			}
		}
		return $settings;
	}

	public function SaveTableSettings($filters = [], $fields = []) {
		TableSettings::I()->Save(
			$this->USER->ID,
			get_class($this->SCRUD),
			$filters,
			$fields
		);
		$this->Redirect(null);
	}

	public function GetTabsOfCreate() {
		$tabs = [
			'main' => [
				'NAME'   => T([
					'en' => 'Element',
					'ru' => 'Элемент',
				]),
				'VIEW'   => 'element_tab_main',
				'ACTIVE' => true,
			],
		];
		foreach ($this->SCRUD->fields as $code => $field) {
			if ($field['TYPE'] === 'INNER') {
				unset($this->SCRUD->composition[$field['GROUP']]['FIELDS'][$code]);
			}
		}
		return $tabs;
	}

	public function GetTabsOfUpdate() {
		$tabs = [
			'element' => [
				'NAME'   => T([
					'en' => 'Element',
					'ru' => 'Элемент',
				]),
				'VIEW'   => 'element_tab_main',
				'ACTIVE' => true,
			],
		];
		foreach ($this->SCRUD->fields as $code => $field) {
			if ($field['TYPE'] === 'INNER') {
				$tabs[$code] = [
					'NAME' => $field['NAME'],
					'VIEW' => 'element_tab_external',
				];
				unset($this->SCRUD->composition[$field['GROUP']]['FIELDS'][$code]);
			}
		}
		return $tabs;
	}

	public function SearchOuter($code, $search, $page = 1) {
		$this->json = true;

		try {
			$field = $this->SCRUD->fields[$code];
			if (empty($field)) {
				throw new Exception("[{$code}] not found");
			}
			if ($field['TYPE'] <> 'OUTER') {
				throw new Exception("[{$code}] is not type OUTER");
			}

			/**@var \BlackFox\SCRUD $Link */
			$Link = $field['LINK']::I();
			$key = $Link->key();

			if (!empty($search)) {
				$filter = ['LOGIC' => 'OR'];
				foreach ($Link->fields as $code => $field) {
					if (!$field['VITAL']) continue;
					if ($field['TYPE'] === 'STRING') {
						$filter["~{$code}"] = $search;
					}
					if ($field['TYPE'] === 'NUMBER') {
						$filter["{$code}"] = $search;
					}
				}
				$filter = [$filter];
			} else {
				$filter = [];
			}

			$data = $Link->Search([
				'FILTER' => $filter,
				'PAGE'   => $page,
				'FIELDS' => ['@@'],
				'LIMIT'  => 5,
			]);

			$results = [];
			foreach ($data['ELEMENTS'] as $element) {
				$results[] = [
					'id'   => $element[$key],
					'text' => "[{$element[$key]}] " . $Link->GetElementTitle($element),
					'link' => $Link->GetAdminUrl() . "?{$key}={$element[$key]}",
				];
			}

			$more = $data['PAGER']['TOTAL'] > $data['PAGER']['CURRENT'] * $data['PAGER']['LIMIT'];

			return [
				'results'    => $results,
				'pagination' => ['more' => $more],
			];
		} catch (Exception $error) {
			return ['ERROR' => $error->GetArray()];
		}
	}

} 