<?php

namespace System;
abstract class ComponentSection extends Component {

	public function SelectMethodForView($request) {
		$element = !empty($request['ID']) || !empty($request['id']);
		$creation = isset($request['NEW']) || isset($request['new']);
		if ($element) {
			$action = 'Element';
			$this->view = 'element';
		} elseif ($creation) {
			$action = 'Creation';
			$this->view = 'creation';
		} else {
			$action = 'Section';
			$this->view = 'section';
		}
		return $action;
	}

	public function Element($ID, $fields = array()) {
		$this->Debug($ID, '$ID');
		$this->Debug($fields, '$fields');
		return array();
	}

	public function Section($filter = array(), $page = 1) {
		$this->Debug($filter, '$filter');
		$this->Debug($page, '$page');
		return array();
	}

	public function Creation($fields = array()) {
		$this->Debug($fields, '$fields');
		return array();
	}
}