<?php
if (!function_exists('T')) {
	/**
	 * It finds language code from $_SESSION['USER']['LANG']
	 * It picks corresponding value from input array
	 *
	 * @param array $variants
	 * @return string
	 */
	function T(array $variants) {
		return $variants[$_SESSION['USER']['LANG']] ?: reset($variants);
	}
}