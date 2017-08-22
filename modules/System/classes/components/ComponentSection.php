<?php
namespace System;
abstract class ComponentSection extends Component {

	public function SelectMethodForView($request) {
		$element = !empty($request['ID']) || !empty($request['id']);
		$creation = isset($request['NEW']) || isset($request['new']);
		if ($element) {
			$action = 'Element';
		} elseif ($creation) {
			$action = 'Creation';
		} else {
			$action = 'Section';
		}
		return $action;
	}

	public function Element($ID, $fields = array()) {
		$this->Debug($ID, '$ID');
		$this->Debug($fields, '$fields');
		$this->view = 'element';
		return array();
	}

	public function Section($filter = array(), $page = 1) {
		$this->Debug($filter, '$filter');
		$this->Debug($page, '$page');
		$this->view = 'section';
		return array();
	}

	public function Creation($fields = array()) {
		$this->Debug($fields, '$fields');
		$this->view = 'creation';
		return array();
	}
}