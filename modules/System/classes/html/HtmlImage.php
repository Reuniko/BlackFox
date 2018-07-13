<?php

namespace System;

class HtmlImage {

	/**
	 * Shows image as adaptive div with 100% width and proportional height.
	 *
	 * @param string $src path to image
	 * @param float $proportion height/width
	 * @param string $position css background-position (optional, default: center center)
	 */
	public static function Proportional($src, $proportion = 1.0, $position = 'center center') {
		?>
		<div style="position: relative; width: 100%;">
			<div style="display: block; padding-top: <?= ceil($proportion * 100) ?>%; "></div>
			<div style="
				position:  absolute;
				top: 0;
				left: 0;
				bottom: 0;
				right: 0;
				background-image: url('<?= $src ?>');
				background-size: cover;
				background-position: <?= $position ?>;
				"></div>
		</div>
		<?
	}

	/**
	 * Shows image as adaptive div with 100% width and specified height.
	 *
	 * @param string $src path to image
	 * @param string $height height with units (for example: '100px')
	 * @param string $position css background-position (optional, default: center center)
	 */
	public static function FixedHeight($src, $height, $position = 'center center') {
		?>
		<div style="position: relative; width: 100%;">
			<div style="display: block; padding-top: <?= $height ?>; "></div>
			<div style="
				position:  absolute;
				top: 0;
				left: 0;
				bottom: 0;
				right: 0;
				background-image: url('<?= $src ?>');
				background-size: cover;
				background-position: <?= $position ?>;
				"></div>
		</div>
		<?
	}

	/**
	 * Shows image as adaptive div with 100% width and 100% height.
	 * Requires to be in container with height.
	 *
	 * @param string $src path to image
	 * @param string $position css background-position (optional, default: center center)
	 */
	public static function Fill($src, $position = 'center center') {
		?>
		<div style="position: relative; width: 100%; height: 100%;">
			<div style="
				position:  absolute;
				top: 0;
				left: 0;
				bottom: 0;
				right: 0;
				background-image: url('<?= $src ?>');
				background-size: cover;
				background-position: <?= $position ?>;
				"></div>
		</div>
		<?
	}

}