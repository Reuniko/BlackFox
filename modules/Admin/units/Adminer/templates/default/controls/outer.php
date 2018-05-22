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
	<div class="col-12 col-sm-6 mb-1">
		<div class="input-group">
			<div class="input-group-prepend">
				<button
					style="width: 40px;"
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
	<div class="col-12 col-sm-6 mb-1">
		<div class="input-group">
			<div class="input-group-prepend">
				<a
					style="width: 40px;"
					class="btn btn-secondary"
					href="<?= ($ID) ? "{$url}?ID={$ID}" : "" ?>"
					data-link-a="FIELDS[<?= $code ?>]"
					title="Открыть элемент"
				><i class="fa fa-external-link-alt"></i></a>
			</div>
			<input
				type="text"
				class="form-control"
				disabled="disabled"
				data-link-span="FIELDS[<?= $code ?>]"
				value="<?= $Link->GetElementTitle($RESULT['DATA'][$code]) ?>"
			>
		</div>
	</div>
</div>

