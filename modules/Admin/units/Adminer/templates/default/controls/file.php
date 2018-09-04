<?
/** @var \Admin\Adminer $this */
/** @var string $code */
/** @var \System\Files $Link */
/** @var array $field */
$Link = $this->SCRUD->types[$code]->info['LINK']::I();
$url = $Link->GetAdminUrl();
$file = $RESULT['DATA'][$code];
$ID = $RESULT['DATA'][$code]['ID'];
?>

<div data-file="">

	<? if (!empty($ID)): ?>
		<div class="mb-1">
			<a
				href="<?= ($ID) ? "{$url}?ID={$ID}" : "" ?>"
				class="btn btn-secondary"
			>№<?= $RESULT['DATA'][$code]['ID'] ?: '...' ?></a>

			<a
				target="_blank"
				href="<?= $file['SRC'] ?>"
				style="color: green"
			><?= $Link->GetElementTitle($file) ?></a>
			(<?= \System\Files::I()->GetPrintableFileSize($file['SIZE']) ?>)

			<label>
				<input
					data-file-delete=""
					type="checkbox"
					name="FIELDS[<?= $code ?>]"
					value=""
				/>
				Удалить
			</label>
		</div>
	<? endif; ?>

	<div
		data-file-selector=""
		style="<?= (!empty($ID)) ? 'display: none;' : '' ?>"
	>
		<label for="<?= $code ?>">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="btn btn-info">
						<i class="fa fa-file"></i>
						<span data-file-name="Выбрать файл">Выбрать файл</span>
					</span>
				</div>
			</div>

			<input
				class="hidden"
				type="file"
				class="form-control"
				id="<?= $code ?>"
				name="FIELDS[<?= $code ?>]"
				placeholder=""
				<?= ($field['DISABLED']) ? 'disabled' : '' ?>
			>
		</label>
	</div>

</div>



