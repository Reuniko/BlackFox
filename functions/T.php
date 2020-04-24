<?php
if (!function_exists('T')) {
	/**
	 * It finds language code
	 * It picks corresponding value from input array
	 *
	 * @param array $variants
	 * @return string
	 */
	function T(array $variants) {
		$code = \BlackFox\Engine::I()->GetLanguage();
		return $variants[$code] ?: reset($variants);
	}
}