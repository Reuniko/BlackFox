<?php

namespace System;

class Pagination extends \System\Unit {

	public $options = [
		'TOTAL'    => [
			'TYPE' => 'integer',
		],
		'CURRENT'  => [
			'TYPE' => 'integer',
		],
		'LIMIT'    => [
			'TYPE' => 'integer',
		],
		'SELECTED' => [
			'TYPE' => 'integer',
		],
		'SPREAD'   => [
			'TYPE'    => 'integer',
			'DEFAULT' => 7,
		],
		'VARIABLE' => [
			'TYPE'    => 'string',
			'DEFAULT' => 'page',
		],
	];

	public function GetActions(array $request = []) {
		return 'Default';
	}

	public function Default() {
		return $this->GetPages(
			$this->PARAMS['TOTAL'],
			$this->PARAMS['CURRENT'],
			$this->PARAMS['LIMIT']
		);
	}

	private function GetPages($total, $current, $limit) {
		$RESULT = [];

		$spread = $this->PARAMS['SPREAD'];
		$current = $current ?: 1;

		$pages_count = (int)ceil($total / $limit);

		$pages = array_fill(1, $pages_count, null);
		$last_was_excluded = false;
		foreach ($pages as $page => $crap) {
			$in_spread = (
				($page === 1)
				|| ($page === $pages_count)
				|| (abs($page - $current) <= $spread)
			);
			if ($in_spread) {
				$RESULT[$page] = [
					'INDEX'  => $page,
					'ACTIVE' => ($page === $current),
				];
				$last_was_excluded = false;
			} elseif (!$last_was_excluded) {
				$RESULT[$page] = [
					'INDEX' => '...',
					'...'   => true,
				];
				$last_was_excluded = true;
			} else {
				continue;
			}
		}
		return $RESULT;
	}
}
