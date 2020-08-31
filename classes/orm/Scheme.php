<?php

namespace BlackFox;

class Scheme {

	/** @var Database */
	public $Database;

	/** @var SCRUD[] */
	public $Tables = [];

	/**
	 * @param SCRUD|string[] $Tables
	 * @throws Exception
	 */
	public function __construct(array $Tables) {
		foreach ($Tables as $Table) {
			if (is_object($Table)) {
				$this->Tables[get_class($Table)] = $Table;
			} else {
				$this->Tables[$Table] = $Table::I();
			}
		}

		foreach ($this->Tables as $code => $Table) {
			if (empty($this->Database))
				$this->Database = $Table->Database;

			if ($this->Database !== $Table->Database)
				throw new Exception("Can't construct scheme: table '{$code}' has different database than table '" . reset($this->Tables)->code . "'");
		}
	}

	public function Compare() {
		$diff = [];
		foreach ($this->Tables as $table_code => $Table) {
			$diff = array_merge($diff, $Table->Compare());
		}
		usort($diff, function ($a, $b) {
			return ($a['PRIORITY'] ?: 0) <=> ($b['PRIORITY'] ?: 0);
		});
		return $diff;
	}

	public function Synchronize() {
		$diff = $this->Compare();
		foreach ($diff as &$instruction) {
			try {
				$this->Database->Query($instruction['SQL']);
				$instruction['STATUS'] = 'SUCCESS';
			} catch (\Exception $error) {
				$instruction['STATUS'] = 'ERROR';
				$instruction['ERROR'] = $error->GetMessage();
			}
		}
		return $diff;
	}
}