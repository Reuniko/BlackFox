<?php

namespace BlackFox;

class SchemeSynchronizer extends \BlackFox\Unit {

	public function GetActions(array $request = []) {
		if ($request['ACTION'] === 'SynchronizeAll') {
			return ['SynchronizeAll'];
		}
		return parent::GetActions($request);
	}

	public function Default() {
		$R['MODE'] = 'Compare';
		foreach ($this->ENGINE->cores as $namespace => $core_absolute_folder) {
			$Core = "{$namespace}\\Core";
			/* @var \BlackFox\ACore $Core */
			$Scheme = $Core::I()->GetScheme();
			if (is_object($Scheme))
				$R['CORES'][$namespace] = $Scheme->Compare();
			else
				$R['CORES_OFF'][$namespace] = true;
		}
		return $R;
	}

	public function RunSQL(string $SQL) {
		$this->ENGINE->Database->Query($SQL);
		return "Success: <pre>{$SQL}</pre>";
	}

	public function SynchronizeAll() {
		$R['MODE'] = 'Synchronize';
		foreach ($this->ENGINE->cores as $namespace => $core_absolute_folder) {
			$Core = "{$namespace}\\Core";
			/* @var \BlackFox\ACore $Core */
			$Scheme = $Core::I()->GetScheme();
			if (is_object($Scheme))
				$R['CORES'][$namespace] = $Scheme->Synchronize();
			else
				$R['CORES_OFF'][$namespace] = true;
		}
		$this->view = 'Default';
		return $R;
	}
}
