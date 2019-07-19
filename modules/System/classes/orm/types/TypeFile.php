<?php

namespace System;

class TypeFile extends TypeOuter {
	public static $TYPE = 'FILE';

	public function ProvideInfoIntegrity() {
		if (empty($this->info['LINK'])) {
			$this->info['LINK'] = 'System\Files';
		}
	}

	public function FormatInputValue($value) {
		if (is_numeric($value)) {
			return (int)$value;
		}
		if (is_array($value)) {
			return $this->info['LINK']::I()->Create($value);
		}
		return null;
	}

	public function PrintValue($value) {
		?>
		<? if (!empty($value['SRC'])): ?>
			<? if (User::I()->InGroup('root')): ?>
				[<a target="_blank" href="/admin/System/Files.php?ID=<?= $value['ID'] ?>"><?= $value['ID'] ?></a>]
			<? endif; ?>
			<a target="_blank" href="<?= $value['SRC'] ?>"><?= $value['NAME'] ?></a>
		<? endif; ?>
		<?
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		$code = $this->info['CODE'];
		/** @var \System\Files $Link */
		$Link = $this->info['LINK']::I();
		$url = $Link->GetAdminUrl();
		$file = $value;
		@$ID = $value['ID'];
		?>

		<div data-file="">

			<? if (!empty($ID)): ?>
				<div class="form-control-plaintext">
					[<a
						href="<?= "{$url}?ID={$ID}" ?>"
						class=""
					>№<?= $ID ?></a>]

					<a
						target="_blank"
						href="<?= $file['SRC'] ?>"
						style="color: green"
					><?= $Link->GetElementTitle($file) ?></a>
					(<?= \System\Files::I()->GetPrintableFileSize($file['SIZE']) ?>)

					<label>
						<input
							class="<?= $class ?>"
							data-file-delete=""
							type="checkbox"
							name="<?= $name ?>"
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
				<label for="<?= $name ?>">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="btn btn-info">
								<i class="fa fa-file"></i>
								<span data-file-name=""><?=T([
								    'en' => 'Select file',
								    'ru' => 'Выбрать файл',
								])?></span>
							</span>
						</div>
					</div>

					<input
						class="d-none invisible <?= $class ?>"
						type="file"
						id="<?= $name ?>"
						name="<?= $name ?>"
						placeholder=""
						<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
					/>
				</label>
			</div>

		</div>


		<?
	}
}