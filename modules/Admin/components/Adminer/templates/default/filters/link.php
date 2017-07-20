<?
/** @var \Admin\Adminer $this */
/** @var string $code */
/** @var \System\SCRUD $Link */
$Link = $this->SCRUD->structure[$code]['LINK']::I();
$url = $Link->GetAdminUrl();
$ID = $RESULT['FILTER'][$code];
?>
<div class="btn-toolbar" style="vertical-align: middle; line-height: 34px;">
	<div class="btn-group">
		<button
			type="button"
			class="form-control"
			onclick="window.open(
				'<?= $url ?>?popup=FILTER[<?= $code ?>]',
				'',
				'height=' + ((screen.height) - 100) + ',width=' + ((screen.width) - 20) + ''
				);"
		>
			<i class="glyphicon glyphicon-search"></i>
		</button>
	</div>
	<div class="btn-group">
		<input
			type="text"
			class="form-control"
			id="<?= $code ?>"
			name="FILTER[<?= $code ?>]"
			placeholder=""
			data-link-input="FILTER[<?= $code ?>]"
			value="<?= $ID ?>"
			<?= ($field['DISABLED']) ? 'disabled' : '' ?>
		>
	</div>
	<div class="btn-group">
		[<a href="<?= ($ID) ? "{$url}?ID={$ID}" : "#" ?>" data-link-a="FILTER[<?= $code ?>]"><?= $RESULT['DATA'][$code]['ID'] ?: '...' ?></a>]
		<span data-link-span="FILTER[<?= $code ?>]">
			<? foreach ($this->SCRUD->structure as $s_code => $s_field): ?>
				<? if ($s_field['SHOW']): ?>
					<?= $RESULT['DATA'][$code][$s_code] ?>
				<? endif; ?>
			<? endforeach; ?>
		</span>
	</div>
</div>






