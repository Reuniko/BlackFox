<?php

namespace System;

class HtmlImage {

	public static function Proportional($src, $height = 1) {
		?>
		<div style="position: relative; width: 100%;">
			<div style="display: block; padding-top: <?= ceil($height * 100) ?>%; "></div>
			<div style="
				position:  absolute;
				top: 0;
				left: 0;
				bottom: 0;
				right: 0;
				background-image: url('<?= $src ?>');
				background-size: cover;
				background-position: center center;
				"></div>
		</div>
		<?
	}

}