<?php

namespace Admin;

class Menu extends \System\Component {
	public function Work() {
		$this->RESULT = require($this->ENGINE->SearchAncestorFile($this->ENGINE->url['path'], '.menu.php'));
		$url = parse_url($_SERVER['REQUEST_URI']);
		foreach ($this->RESULT as &$element1) {
			if ($element1['LINK'] == $url['path']) {
				$element1['ACTIVE'] = true;
			}
			if (is_array($element1['CHILDREN'])) {
				foreach ($element1['CHILDREN'] as &$element2) {
					if ($element2['LINK'] == $url['path']) {
						$element1['ACTIVE'] = true;
						$element2['ACTIVE'] = true;
					}
				}
			}
		}
		return $this->RESULT;
	}

	public function SelectMethodForAction($request = array()) {
		return 'Work';
	}
}
