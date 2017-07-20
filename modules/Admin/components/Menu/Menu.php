<?php
namespace Admin;

class Menu extends \System\Component {
	public function Work() {
		$this->RESULT = require($this->ENGINE->GetRootFile($_SERVER['REQUEST_URI'], '.menu.php'));
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
