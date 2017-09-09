<?php

namespace System;

abstract class ComponentSection extends Component {

	public function SelectMethodForView($request = []) {
		if (isset($request['EDIT'])) {
			$this->view = 'form';
			return 'Editing';
		}
		if (isset($request['NEW'])) {
			$this->view = 'form';
			return 'Creating';
		}
		if (!empty($request['ID'])) {
			$this->view = 'element';
			return 'Element';
		}
		$this->view = 'section';
		return 'Section';
	}

	/**
	 * Подготавливает данные для отображения страницы со списком элементов.
	 * Контроллирует доступ, кидает исключения.
	 *
	 * @param array $filter
	 * @param int $page
	 * @return array
	 * @throws \Exception
	 */
	public function Section($filter = [], $page = 1) {
		$this->Debug($filter, 'Section $filter');
		$this->Debug($page, 'Section $page');
		return [];
	}

	/**
	 * Подготавливает данные для отображения страницы с просмотра элемента.
	 * Контроллирует доступ, кидает исключения.
	 *
	 * @param $ID
	 * @param array $fields
	 * @return array
	 * @throws \Exception
	 */
	public function Element($ID, $fields = []) {
		$this->Debug($ID, 'Element $ID');
		$this->Debug($fields, 'Element $fields');
		return [];
	}

	/**
	 * Подготавливает данные для отображения формы редактирования элемента.
	 * Контроллирует доступ, кидает исключения.
	 *
	 * @param $ID
	 * @param array $fields
	 * @return array
	 * @throws \Exception
	 */
	public function Editing($ID, $fields = []) {
		$this->Debug($ID, 'Editing $ID');
		$this->Debug($fields, 'Editing $fields');
		return [];
	}

	/**
	 * Подготавливает данные для отображения формы добавления элемента.
	 * Контроллирует доступ, кидает исключения.
	 *
	 * @param array $fields
	 * @return array
	 * @throws \Exception
	 */
	public function Creating($fields = []) {
		$this->Debug($fields, 'Creating $fields');
		return [];
	}
}