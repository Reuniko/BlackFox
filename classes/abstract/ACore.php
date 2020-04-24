<?php

namespace BlackFox;

abstract class ACore extends Instanceable {

	public $description = 'Redefine $name, $description и $version';
	public $version = '1.0';

	/**
	 * Scans info about all files in specified directory and all subdirectories.
	 *
	 * @param string $directory absolute path to directory
	 * @param int $depth deepness of scan (optional)
	 * @return array list of all files as pathinfo structs: {dirname, basename, extension, filename}
	 */
	public function ScanDirectoryRecursive($directory, $depth = null) {
		$files = [];
		$names = @scandir($directory);
		if (!empty($names)) {
			foreach ($names as $name) {
				if ($name === '.' or $name === '..') {
					continue;
				}
				if (is_dir("{$directory}/{$name}")) {
					if ($depth === 0) {
						continue;
					}
					$files += $this->ScanDirectoryRecursive("{$directory}/{$name}", $depth ? ($depth - 1) : $depth);
				} else {
					$files["{$directory}/{$name}"] = pathinfo("{$directory}/{$name}");
				}
			}
		}
		return $files;
	}

	/**
	 * Method returns classes of this module.
	 *
	 * @return array dictionary: key - class name (with namespace), value - absolute path to php-file with class
	 */
	public function GetClasses() {
		list($namespace) = explode('\\', get_called_class());
		$core_absolute_path = Engine::I()->GetAbsolutePath(Engine::I()->cores[$namespace]);

		$files = [];
		$files += $this->ScanDirectoryRecursive("{$core_absolute_path}/classes");
		$files += $this->ScanDirectoryRecursive("{$core_absolute_path}/units", 1);

		$classes = [];
		foreach ($files as $path => $file) {
			if ($file['extension'] === 'php') {
				$classes[$namespace . '\\' . $file['filename']] = $path;
			}
		}
		return $classes;
	}

	/**
	 * Use this method to synchronize table structures
	 * See children for examples
	 */
	public function Upgrade() {
		// override
	}

	public function Load() {
		// override
	}

	/**
	 * Compiles and return the tree of menu for administrative section
	 * See children for examples
	 *
	 * @return array
	 */
	public function Menu() {
		return [];
	}
}