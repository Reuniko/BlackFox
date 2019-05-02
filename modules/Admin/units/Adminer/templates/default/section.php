<?php /** @var \Admin\Adminer $this */ ?>
<div class="adminer">

	<? require($this->Path('section_settings.php')); ?>

	<? if (!$this->frame): ?>
		<? require($this->Path('filter.php')) ?>
	<? endif; ?>

	<div class="my-2 buttons">

		<a
			class="btn btn-light float-right"
			data-toggle="modal"
			data-target="#section-settings"
		>
			<i class="fa fa-cog"></i>
		</a>

		<? if (in_array($RESULT['MODE'], ['SECTION'])): ?>
			<a class="btn btn-success" href="?NEW&<?= http_build_query($_GET) ?>">
				<i class="fa fa-plus"></i>
				<?= T([
					'en' => 'Add',
					'ru' => 'Создать',
				]) ?>
			</a>
		<? endif; ?>

		<div class="clearfix"></div>

	</div>

	<form method="post">

		<table id="data" class="table table-bordered table-hover table-responsive-sm">
			<tr>
				<th class="sort" width="1%"><span></span></th>
				<?
				$get = $_GET;
				unset($get['SORT']);
				$url = $this->SanitizeUrl('?' . http_build_query($get));
				?>
				<? foreach ($RESULT['STRUCTURE']['FIELDS'] as $structure_code => $field): ?>
					<? if (!isset($this->SCRUD->structure[$structure_code])) continue; ?>
					<?
					$direction = (($RESULT['SORT'][$structure_code] === 'ASC') ? 'DESC' : 'ASC');
					$sort_href = $url . "&SORT[{$structure_code}]={$direction}";
					$is_numeric = in_array($field['TYPE'], [
						'NUMBER',
						'FLOAT',
						'LINK',
					]);
					$icon_class = $is_numeric ? 'numeric' : 'alpha';
					?>
					<th class="sort<?= isset($RESULT['SORT'][$structure_code]) ? ' active' : '' ?>">
						<a href="<?= $sort_href ?>">
							<? if ($RESULT['SORT'][$structure_code]): ?>
								<div class="sort-icon">
									<? if ($RESULT['SORT'][$structure_code] === 'ASC'): ?>
										<i class="fa fa-sort-<?= $icon_class ?>-down"></i>
									<? endif; ?>
									<? if ($RESULT['SORT'][$structure_code] === 'DESC'): ?>
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
					<td colspan="<?= 1 + count($RESULT['STRUCTURE']['FIELDS']) ?>" class="text-center">
						<?= T([
							'en' => '- no data -',
							'ru' => '- нет данных -',
						]) ?>
					</td>
				</tr>
			<? endif; ?>
			<? foreach ($RESULT['DATA']['ELEMENTS'] as $row): ?>
				<?
				$href = '?' . http_build_query(array_merge($_GET, ['ID' => $row['ID']]));
				$ondblclick = "window.location.href='{$href}'";
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
						<? if (!isset($this->SCRUD->structure[$code])) continue; ?>
						<td>
							<div class="table-content table-content-<?= $this->SCRUD->structure[$code]['TYPE'] ?>">
								<?
								ob_start();
								// -------------------------------------------------
								$this->SCRUD->structure[$code]->PrintValue($row[$code]);
								// -------------------------------------------------
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
			<? if (in_array($RESULT['MODE'], ['SECTION'])): ?>
				<tr>
					<td colspan="<?= 1 + count($RESULT['STRUCTURE']['FIELDS']) ?>">
						<button
							class="btn btn-danger"
							type="submit"
							name="ACTION"
							value="Delete"
							data-confirm="<?= T([
								'en' => 'Confirm deletion of selected elements',
								'ru' => 'Подтвердите удаление выделенных элементов',
							]) ?>"
						>
							<i class="fa fa-trash"></i>
							<?=T([
							    'en' => 'Delete selected',
							    'ru' => 'Удалить выделенные',
							])?>
						</button>
					</td>
				</tr>
			<? endif; ?>
		</table>

	</form>

	<? require($this->Path('pager.php')) ?>
</div>
