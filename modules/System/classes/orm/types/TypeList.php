<?php

namespace System;

class TypeList extends TypeText {
	public $name = 'List';
	public $code = 'LIST';

	public function FormatInputValue($value, $info = []) {
		$value = is_array($value) ? $value : [$value];
		$value = array_filter($value, 'strlen');
		$value = json_encode($value, JSON_UNESCAPED_UNICODE);
		return parent::FormatInputValue($value, $info);
	}

	public function FormatOutputValue($element, $code, $info) {
		$element[$code] = json_decode($element[$code], true);
		if (json_last_error()) {
			$element[$code] = [];
		}
		return parent::FormatOutputValue($element, $code, $info);
	}

}