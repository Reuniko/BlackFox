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
				<?
				/** @var \System\Files $Link */
				$Link = $this->info['LINK']::I();
				?>
				[<a target="_blank" href="<?= $Link->GetAdminUrl() ?>?ID=<?= $value['ID'] ?>"><?= $value['ID'] ?></a>]
			<? endif; ?>
			<a target="_blank" href="<?= $value['SRC'] ?>"><?= $value['NAME'] ?></a>
		<? endif; ?>
		<?
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		Engine::I()->AddHeaderScript(Engine::I()->GetRelativePath(__DIR__ . '/TypeFile.js'));
		/** @var \System\Files $Link */
		$Link = $this->info['LINK']::I();
		$url = $Link->GetAdminUrl();
		$ID = isset($value['ID']) ? $value['ID'] : null;
		?>

		<div data-file="">

			<? if (!empty($ID)): ?>
				<div class="form-control-plaintext">
					[<a href="<?= "{$url}?ID={$ID}" ?>"><?= $ID ?></a>]

					<a
						target="_blank"
						href="<?= $value['SRC'] ?>"
						style="color: green"
					><?= $Link->GetElementTitle($value) ?></a>
					(<?= $Link->GetPrintableFileSize($value['SIZE']) ?>)

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
								<span data-file-name=""><?= T([
										'en' => 'Select file',
										'ru' => 'Выбрать файл',
									]) ?></span>
							</span>
						</div>
					</div>

					<input
						class="d-none invisible <?= $class ?>"
						type="file"
						id="<?= $name ?>"
						name="<?= $name ?>"
						placeholder=""
						<?= (!empty($ID)) ? 'disabled' : '' ?>
					/>
				</label>
			</div>

		</div>


		<?
	}
}