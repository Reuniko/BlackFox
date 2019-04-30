<?php
if (!function_exists('T')) {
	/**
	 * Picks
	 *
	 * @param array $variants
	 * @return string
	 */
	function T($variants) {
		return $variants[$_SESSION['USER']['LANG']] ?: reset($variants);
	}
}