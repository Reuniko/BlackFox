<?php

namespace System;

class Database extends Instanceable {

	private static $TRANSACTION_DEPTH;

	private $host;
	private $port;
	private $database;
	private $user;
	private $password;
	private $charset;

	/**
	 * @var \mysqli
	 */
	private $link;
	private $result;

	public function __construct($params = []) {

		$this->host = $params['HOST'];
		$this->port = $params['PORT'];
		$this->user = $params['USER'];
		$this->password = $params['PASSWORD'];
		$this->database = $params['DATABASE'];
		$this->charset = $params['CHARSET'] ?: 'utf8';

		$this->Connect();
	}

	private function Connect() {
		$this->link = mysqli_connect($this->host, $this->user, $this->password, $this->database, $this->port);
		if ($this->link === false) {
			throw new Exception(mysqli_connect_error());
		}
		mysqli_set_charset($this->link, $this->charset);
	}

	public function Query($SQL, $key = null) {
		$this->result = mysqli_query($this->link, $SQL);
		if ($this->result === false) {
			// TODO: log error to text file
			throw new ExceptionSQL(mysqli_error($this->link), $SQL);
		}
		if ($this->result === true) {
			return mysqli_insert_id($this->link) ?: true;
		}
		if (is_object($this->result)) {
			$data = [];
			while ($row = mysqli_fetch_assoc($this->result)) {
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
		//debug($data, 'Escape $data', 'log');
		$answer = mysqli_real_escape_string($this->link, $data);
		//$answer = str_replace('\0', ' ', $answer);
		//debug($answer, 'Escape $answer', 'log');
		return $answer;
	}

	public function StartTransaction() {
		if (empty(self::$TRANSACTION_DEPTH)) {
			$this->Query('START TRANSACTION');
		}
		self::$TRANSACTION_DEPTH++;
	}

	public function Rollback() {
		$this->Query('ROLLBACK');
		self::$TRANSACTION_DEPTH = 0;
	}

	public function Commit() {
		self::$TRANSACTION_DEPTH--;
		if (self::$TRANSACTION_DEPTH < 0) {
			self::$TRANSACTION_DEPTH = 0;
		}
		if (empty(self::$TRANSACTION_DEPTH)) {
			$this->Query('COMMIT');
		}
	}
}