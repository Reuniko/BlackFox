<?php /** @var \Admin\Adminer $this */ ?>
<div class="adminer">

	<? require($this->Path('section_settings.php')); ?>
	<? if (!$this->frame): ?>
		<? require($this->Path('filter.php')) ?>
	<? endif; ?>

	<? if (in_array($RESULT['MODE'], ['SECTION'])): ?>
		<div class="my-2 buttons">

			<a
				class="btn btn-light float-right"
				data-toggle="modal"
				data-target="#section-settings"
			>
				<i class="fa fa-cog"></i>
			</a>

			<a class="btn btn-success" href="?NEW&<?= http_build_query($_GET) ?>">
				<i class="fa fa-plus"></i>
				Создать
			</a>

		</div>
	<? endif; ?>

	<form method="post">

		<table id="data" class="table table-bordered table-hover table-responsive-sm">
			<tr>
				<? if ($RESULT['MODE'] <> 'POPUP'): ?>
					<th class="sort" width="1%"><span></span></th>
				<? endif; ?>
				<?
				$get = $_GET;
				unset($get['SORT']);
				$url = $this->SanitizeUrl('?' . http_build_query($get));
				?>
				<? foreach ($RESULT['STRUCTURE']['FIELDS'] as $structure_code => $field): ?>
					<?
					$direction = (($_GET['SORT'][$structure_code] === 'ASC') ? 'DESC' : 'ASC');
					$sort_href = $url . "&SORT[{$structure_code}]={$direction}";
					$is_numeric = in_array($field['TYPE'], [
						'NUMBER',
						'FLOAT',
						'LINK',
					]);
					$icon_class = $is_numeric ? 'numeric' : 'alpha';
					?>
					<th class="sort<?= isset($_GET['SORT'][$structure_code]) ? ' active' : '' ?>">
						<a href="<?= $sort_href ?>">
							<? if ($_GET['SORT'][$structure_code]): ?>
								<div class="sort-icon">
									<? if ($_GET['SORT'][$structure_code] === 'ASC'): ?>
										<i class="fa fa-sort-<?= $icon_class ?>-down"></i>
									<? endif; ?>
									<? if ($_GET['SORT'][$structure_code] === 'DESC'): ?>
										<i class="fa fa-sort-<?= $icon_class ?>-up"></i>
									<? endif; ?>
								</div>
							<? endif; ?>
							<?= $field['NAME'] ?>
						</a>
					</th>
				<? endforeach; ?>
			</tr>
			<? if (empty($RESULT['DATA']['ELEMENTS'])): ?>
				<tr>
					<td colspan="<?= 1 + count($RESULT['STRUCTURE']['FIELDS']) ?>">
						<center>
							- нет данных -
						</center>
					</td>
				</tr>
			<? endif; ?>
			<? foreach ($RESULT['DATA']['ELEMENTS'] as $row): ?>
				<?
				// $href = "?ID={$row['ID']}";
				$href = '?' . http_build_query(array_merge($_GET, ['ID' => $row['ID']]));
				$ondblclick = "window.location.href='{$href}'";

				if ($RESULT['MODE'] === 'POPUP') {
					$script = "$(window.opener.document)
						.find('[data-link-input=\'{$RESULT['POPUP']}\']').val('{$row['ID']}').end()
						.find('[data-link-a=\'{$RESULT['POPUP']}\']').attr('href', '{$this->SCRUD->GetAdminUrl()}?ID={$row['ID']}').end()
						.find('[data-link-span=\'{$RESULT['POPUP']}\']').val('{$this->SCRUD->GetElementTitle($row)}').end()
						;window.close();";
					$href = "javascript:{$script}";
					$ondblclick = $script;
				}
				?>
				<tr ondblclick="<?= $ondblclick ?>">
					<? if ($RESULT['MODE'] <> 'POPUP'): ?>
						<td class="p-2">
							<input
								type="checkbox"
								name="ID[]"
								value="<?= $row['ID'] ?>"
							/>
						</td>
					<? endif; ?>
					<? foreach ($RESULT['STRUCTURE']['FIELDS'] as $code => $field): ?>
						<td>
							<div class="table-content table-content-<?= $this->SCRUD->structure[$code]['TYPE'] ?>">
								<?
								$value = $row[$code];
								$inc = strtolower($field['VIEW']) ?: strtolower($field['TYPE']);
								ob_start();
								try {
									require($this->Path('cells/' . $inc . '.php'));
								} catch (\Exception $error) {
									require($this->Path('cells/' . '_default' . '.php'));
								}
								$content = ob_get_clean();
								?>
								<? if ($this->SCRUD->structure[$code]['PRIMARY']): ?>
									<a href="<?= $href ?>"><?= $content ?></a>
								<? else: ?>
									<?= $content ?>
								<? endif; ?>
							</div>
						</td>
					<? endforeach; ?>
				</tr>
			<? endforeach; ?>
			<tr>
				<td colspan="<?= 1 + count($RESULT['STRUCTURE']['FIELDS']) ?>">
					<button
						class="btn btn-danger"
						type="submit"
						name="ACTION"
						value="Delete"
						data-confirm="Подтвердите удаление выделенных элементов"
					>
						<i class="fa fa-trash"></i>
						Удалить выделенные
					</button>
				</td>
			</tr>
		</table>

	</form>

	<? require($this->Path('pager.php')) ?>
</div>
