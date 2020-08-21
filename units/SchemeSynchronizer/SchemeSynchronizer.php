<?php

namespace BlackFox;

class SchemeSynchronizer extends \BlackFox\Unit {

	public function GetActions(array $request = []) {
		if ($request['ACTION'] === 'SynchronizeAll') {
			return ['SynchronizeAll'];
		}
		return parent::GetActions($request);
	}

	public function RunSQL(string $SQL) {
		$this->ENGINE->Database->Query($SQL);
		return "Success: <pre>{$SQL}</pre>";
	}

	public function Default() {
		$R['MODE'] = 'Compare';
		$R['CORES'] = $this->Do(false);
		$this->view = 'Default';
		return $R;
	}

	public function SynchronizeAll() {
		$R['MODE'] = 'Synchronize';
		$R['CORES'] = $this->Do(true);
		$this->view = 'Default';
		return $R;
	}

	private function Do(bool $synchronize) {
		$R = [];
		foreach ($this->ENGINE->cores as $namespace => $core_absolute_folder) {
			/* @var \BlackFox\ACore $Core */
			$Core = "{$namespace}\\Core";
			try {
				$Scheme = $Core::I()->GetScheme();
				if (!$synchronize) {
					$R[$namespace]['DIFFS'] = $Scheme->Compare();
				} else {
					$R[$namespace]['DIFFS'] = $Scheme->Synchronize();
				}
			} catch (\Exception $error) {
				$R[$namespace]['ERROR'] = $error->GetMessage();
				continue;
			}
		}
		return $R;
	}
}
