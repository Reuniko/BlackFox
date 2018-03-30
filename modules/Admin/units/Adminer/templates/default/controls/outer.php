<?
/** @var \Admin\Adminer $this */
/** @var string $code */
/** @var \System\SCRUD $Link */
$Link = $this->SCRUD->structure[$code]['LINK']::I();
$url = $Link->GetAdminUrl();
if (is_array($RESULT['DATA'][$code])) {
	$ID = $RESULT['DATA'][$code]['ID'];
} else {
	$ID = $RESULT['DATA'][$code];
}
?>

<div class="row">
	<div class="col">
		<div class="btn-toolbar">
			<div class="btn-group">
				<button
					type="button"
					class="btn btn-secondary"
					title="Выбрать элемент"
					<?= ($field['DISABLED']) ? 'disabled' : '' ?>
					onclick="window.open(
						'<?= $url ?>?popup=FIELDS[<?= $code ?>]',
						'',
						'height=' + ((screen.height) - 100) + ',width=' + ((screen.width) - 20) + ''
						);"
				>
					<i class="fa fa-search"></i>
				</button>
			</div>
			<div class="btn-group">
				<input
					type="text"
					class="form-control"
					width="100px"
					id="<?= $code ?>"
					name="FIELDS[<?= $code ?>]"
					placeholder=""
					data-link-input="FIELDS[<?= $code ?>]"
					value="<?= $ID ?>"
					<?= ($field['DISABLED']) ? 'disabled' : '' ?>
				>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="btn-toolbar">
			<div class="btn-group">
				<a
					class="btn btn-secondary"
					href="<?= ($ID) ? "{$url}?ID={$ID}" : "" ?>"
					data-link-a="FIELDS[<?= $code ?>]"
					title="Открыть элемент"
				><i class="fa fa-external-link-alt"></i></a>
			</div>
			<div class="btn-group">
				<? $display = '' ?>
				<? foreach ($Link->structure as $s_code => $s_field): ?>
					<? if ($s_field['SHOW'] and isset($RESULT['DATA'][$code][$s_code])): ?>
						<? $display .= $RESULT['DATA'][$code][$s_code] ?>
					<? endif; ?>
				<? endforeach; ?>
				<input
					type="text"
					class="form-control"
					disabled="disabled"
					data-link-span="FIELDS[<?= $code ?>]"
					value="<?= $display ?>"
				>
			</div>
		</div>
	</div>
</div>

