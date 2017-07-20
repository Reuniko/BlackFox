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
		<label for="<?= $code ?>">
			<span href="#" class="btn btn-default">
				<i class="glyphicon glyphicon-picture"></i>
				Выбрать файл
			</span>
			<input
				style="visibility: hidden; display: none"
				type="file"
				class="form-control"
				id="<?= $code ?>"
				name="FIELDS[<?= $code ?>]"
				placeholder=""
				value="<?= $RESULT['DATA'][$code] ?>"
				<?= ($field['DISABLED']) ? 'disabled' : '' ?>
			>
		</label>
	</div>

	<div class="btn-group">

	</div>

	<? if (!empty($RESULT['DATA'][$code])): ?>
		<div class="btn-group">
			[<a href="<?= ($ID) ? "{$url}?ID={$ID}" : "" ?>" data-link-a="FIELDS[<?= $code ?>]"><?= $RESULT['DATA'][$code]['ID'] ?: '...' ?></a>]
			<a target="_blank" href="<?=$RESULT['DATA'][$code]['SRC']?>" style="color: green">
				<span data-link-span="FIELDS[<?= $code ?>]">
					<? foreach ($Link->structure as $s_code => $s_field): ?>
						<? if ($s_field['SHOW']): ?>
							<?= $RESULT['DATA'][$code][$s_code] ?>
						<? endif; ?>
					<? endforeach; ?>
				</span>
			</a>
		</div>
	<? endif; ?>

</div>
