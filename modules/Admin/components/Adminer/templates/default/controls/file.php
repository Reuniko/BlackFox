<?
/** @var \Admin\Adminer $this */
/** @var string $code */
/** @var \System\SCRUD $Link */
/** @var array $field */
$Link = $this->SCRUD->structure[$code]['LINK']::I();
$url = $Link->GetAdminUrl();
$file = $RESULT['DATA'][$code];
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
				<?= ($field['DISABLED']) ? 'disabled' : '' ?>
			>
		</label>
	</div>

	<? if (!empty($file)): ?>

		<div class="btn-group">
			[<a href="<?= ($ID) ? "{$url}?ID={$ID}" : "" ?>" data-link-a="FIELDS[<?= $code ?>]"><?= $RESULT['DATA'][$code]['ID'] ?: '...' ?></a>]
			<a target="_blank" href="<?= $file['SRC'] ?>" style="color: green">
				<span data-link-span="FIELDS[<?= $code ?>]">
					<? foreach ($Link->structure as $s_code => $s_field): ?>
						<? if ($s_field['SHOW']): ?>
							<?= $file[$s_code] ?>
						<? endif; ?>
					<? endforeach; ?>
				</span>
			</a>
		</div>
	<? endif; ?>
</div>

<? if (substr($file['TYPE'], 0, 5) === 'image'): ?>
	<div>
		<a class="thumbnail" href="<?= $file['SRC'] ?>" target="_blank">
			<img src="<?= $file['SRC'] ?>" style="max-height: 100px;"/>
		</a>
	</div>
<? endif; ?>
