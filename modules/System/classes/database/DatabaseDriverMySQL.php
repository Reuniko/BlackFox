<?php

namespace System;

class DatabaseDriverMySQL extends Database {

	/**
	 * @var \mysqli
	 */
	private $link;

	public function __construct($params = []) {

		$this->link = mysqli_connect(
			$params['HOST'],
			$params['USER'],
			$params['PASSWORD'],
			$params['DATABASE'],
			$params['PORT'],
			$params['HOST']
		);

		if ($this->link === false) {
			throw new Exception(mysqli_connect_error());
		}
		mysqli_set_charset($this->link, $params['CHARSET'] ?: 'utf8');
	}

	public function Query($SQL, $key = null) {
		$result = mysqli_query($this->link, $SQL);
		if ($result === false) {
			throw new ExceptionSQL(mysqli_error($this->link), $SQL);
		}
		if ($result === true) {
			return mysqli_insert_id($this->link) ?: true;
		}
		if (is_object($result)) {
			$data = [];
			while ($row = mysqli_fetch_assoc($result)) {
				if (isset($key) and isset($row[$key])) {
					$data[$row[$key]] = $row;
				} else {
					$data[] = $row;
				}
			}
			return $data;
		}
	}

	public function Escape($data) {
		if (is_null($data)) {
			return null;
		}
		return mysqli_real_escape_string($this->link, $data);
	}
}