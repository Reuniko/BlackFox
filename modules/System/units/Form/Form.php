<?php

namespace System;

class Form extends \System\Unit {

	public $options = [
		'SCRUD'   => [
			'TYPE' => 'object',
			'NAME' => 'Сущность',
		],
		'FIELDS'  => [
			'TYPE' => 'array',
			'NAME' => 'Поля',
		],
		'DATA'    => [
			'TYPE' => 'array',
			'NAME' => 'Данные',
		],
		'ELEMENT' => [
			'TYPE'    => 'string',
			'NAME'    => 'Имя элемента',
			'DEFAULT' => 'ELEMENT',
		],
		'CLASS_GROUP' => [
			'TYPE'    => 'string',
			'DEFAULT' => 'form-group row',
		],
		'CLASS_LABEL' => [
			'TYPE'    => 'string',
			'DEFAULT' => 'col-sm-3 col-form-label text-sm-right',
		],
		'CLASS_BLOCK' => [
			'TYPE'    => 'string',
			'DEFAULT' => 'col-sm-8',
		],
		'CLASS_CONTROL' => [
			'TYPE'    => 'string',
			'DEFAULT' => 'form-control',
		],
	];

	public function GetActions(array $request = []) {
		return 'Default';
	}

	public function Default() {
		$R = $this->PARAMS;
		$fields = [];
		foreach ($R['FIELDS'] as $key => $value) {
			if (is_string($value)) {
				$fields[$value] = $R['SCRUD']->structure[$value];
			} else {
				$fields[$key] = $value;
			}
		}
		$R['FIELDS'] = $fields;
		return $R;
	}
}
