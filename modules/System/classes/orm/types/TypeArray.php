<?php

namespace System;

class TypeArray extends TypeText {
	public static $TYPE = 'ARRAY';

	public function FormatInputValue($value) {
		$value = is_array($value) ? $value : [$value];
		$value = json_encode($value, JSON_UNESCAPED_UNICODE);
		return parent::FormatInputValue($value);
	}

	public function FormatOutputValue($element) {
		$code = $this->info['CODE'];
		$element[$code] = json_decode($element[$code], true);
		if (json_last_error()) {
			$element[$code] = [];
		}
		return $element;
	}

}