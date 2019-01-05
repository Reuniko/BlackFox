<?php

namespace System;

class TypeFile extends TypeOuter {
	public static $name = 'File';
	public static $code = 'FILE';

	public function GetStructureStringType() {
		return 'int';
	}

	public function ProvideInfoIntegrity($info = []) {
		if (empty($info['LINK'])) {
			$info['LINK'] = 'System\Files';
		}
		return $info;
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
		<? if (!empty($value)): ?>
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
		@$ID = $value['ID'] ?: $value ?: null;
		?>

		<div data-file="">

			<? if (!empty($ID)): ?>
				<div class="mb-1">
					<a
						href="<?= ($ID) ? "{$url}?ID={$ID}" : "" ?>"
						class="btn btn-secondary"
					>№<?= $ID ?: '...' ?></a>

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
								<span data-file-name="Выбрать файл">Выбрать файл</span>
							</span>
						</div>
					</div>

					<input
						class="hidden <?= $class ?>"
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