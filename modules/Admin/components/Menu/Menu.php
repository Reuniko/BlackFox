<?php

namespace Admin;

class Menu extends \System\Component {
	public function Work() {
		$url = parse_url($_SERVER['REQUEST_URI']);
		$this->RESULT = require($this->ENGINE->SearchAncestorFile($this->ENGINE->url['path'], '.menu.php'));
		$url = parse_url($_SERVER['REQUEST_URI']);
		foreach ($this->RESULT as &$element) {
			if ($element['LINK'] === $url['path']) {
				$element['ACTIVE'] = true;
			}
		}
		return $this->RESULT;
	}

	public function SelectMethodForAction($request = array()) {
		return 'Work';
	}
}
