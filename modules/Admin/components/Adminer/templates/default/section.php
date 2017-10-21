<?php /** @var \Admin\Adminer $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>

<div class="adminer">

	<? require($this->Path('section_settings.php')); ?>
	<? require($this->Path('filter.php')) ?>

	<? if (in_array($RESULT['MODE'], ['SECTION'])): ?>
		<div class="margin buttons">

			<a
				data-filter-settings=""
				class="btn btn-default"
				data-toggle="modal"
				data-target="#section-settings"
			>
				<i class="glyphicon glyphicon-cog"></i>
			</a>

			<a class="btn btn-success" href="?NEW">
				<i class="glyphicon glyphicon-plus"></i>
				Создать
			</a>

		</div>
	<? endif; ?>

	<br/>

	<table id="data" class="table table-bordered table-hover">
		<tr>
			<th class="sort" width="1%"><span></span></th>
			<?
			$get = $_GET;
			unset($get['SORT']);
			$url = $this->SanitizeUrl('?' . http_build_query($get));
			?>
			<? foreach ($RESULT['FIELDS'] as $structure_code => $field): ?>
				<?
				$direction = (($_GET['SORT'][$structure_code] === 'ASC') ? 'DESC' : 'ASC');
				$sort_href = $url . "&SORT[{$structure_code}]={$direction}";
				?>
				<th class="sort <?= isset($_GET['SORT'][$structure_code]) ? 'active' : '' ?>">
					<a href="<?= $sort_href ?>">
						<? if ($_GET['SORT'][$structure_code] === 'ASC'): ?>
							<i class="glyphicon glyphicon-sort-by-attributes"></i>
						<? endif; ?>
						<? if ($_GET['SORT'][$structure_code] === 'DESC'): ?>
							<i class="glyphicon glyphicon-sort-by-attributes-alt"></i>
						<? endif; ?>
						<?= $field['NAME'] ?>
					</a>
				</th>
			<? endforeach; ?>
		</tr>
		<? if (empty($RESULT['DATA']['ELEMENTS'])): ?>
			<tr>
				<td colspan="<?= 1 + count($RESULT['FIELDS']) ?>">
					<center>
						- нет данных -
					</center>
				</td>
			</tr>
		<? endif; ?>
		<? foreach ($RESULT['DATA']['ELEMENTS'] as $row): ?>
			<tr ondblclick="window.location.href='?ID=<?= $row['ID'] ?>'">
				<td>
					<input type="checkbox" name="ELEMENT[]" value="<?= $row['ID'] ?>">
				</td>
				<? foreach ($RESULT['FIELDS'] as $code => $field): ?>
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
								<? if ($RESULT['MODE'] === 'SECTION'): ?>
									<a href="?ID=<?= $row[$code] ?>"><?= $content ?></a>
								<? endif; ?>
								<? if ($RESULT['MODE'] === 'POPUP'): ?>
									<?
									$display = [];
									foreach ($this->SCRUD->structure as $structure_code => $structure_field) {
										if ($structure_field['SHOW']) {
											$display[] = $row[$structure_code];
										}
									}
									$display = implode(' ', $display);
									?>
									<a href="javascript:
	$(window.opener.document)
	.find('[data-link-input=\'<?= $RESULT['POPUP'] ?>\']').val('<?= $row[$code] ?>').end()
	.find('[data-link-a=\'<?= $RESULT['POPUP'] ?>\']').attr('href', '<?= $this->SCRUD->GetAdminUrl() ?>?ID=<?= $row[$code] ?>').text('<?= $row[$code] ?>').end()
	.find('[data-link-span=\'<?= $RESULT['POPUP'] ?>\']').text('<?= $display ?>').end()
	;
	window.close();
											"><?= $content ?></a>
								<? endif; ?>
							<? else: ?>
								<?= $content ?>
							<? endif; ?>
						</div>
					</td>
				<? endforeach; ?>
			</tr>
		<? endforeach; ?>
	</table>

	<? require($this->Path('pager.php')) ?>
</div>
