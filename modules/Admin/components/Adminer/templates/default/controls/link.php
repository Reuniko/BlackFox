<?
/** @var \Admin\Adminer $this */
/** @var string $code */
/** @var \System\SCRUD $Link */
$Link = $this->SCRUD->structure[$code]['LINK']::I();
$url = $Link->GetAdminUrl();
$ID = $RESULT['DATA'][$code]['ID'];
?>
<div class="btn-toolbar" style="vertical-align: middle; line-height: 34px;">
	<div class="btn-group">
		<button
			type="button"
			class="form-control"
			onclick="window.open(
				'<?= $url ?>?popup=FIELDS[<?= $code ?>]',
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
			name="FIELDS[<?= $code ?>]"
			placeholder=""
			data-link-input="FIELDS[<?= $code ?>]"
			value="<?= $ID ?>"
			<?= ($field['DISABLED']) ? 'disabled' : '' ?>
		>
	</div>
	<div class="btn-group">
		[<a href="<?= ($ID) ? "{$url}?ID={$ID}" : "" ?>" data-link-a="FIELDS[<?= $code ?>]"><?= $RESULT['DATA'][$code]['ID'] ?: '...' ?></a>]
		<span data-link-span="FIELDS[<?= $code ?>]">
			<? foreach ($Link->structure as $s_code => $s_field): ?>
				<? if ($s_field['SHOW']): ?>
					<?= $RESULT['DATA'][$code][$s_code] ?>
				<? endif; ?>
			<? endforeach; ?>
		</span>
	</div>
</div>






