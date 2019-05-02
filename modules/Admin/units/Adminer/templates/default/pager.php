<? /** @var \System\Unit $this */ ?>
<? /** @var array $RESULT */ ?>
<? $this->Debug($RESULT['DATA']['PAGER'], 'PAGER'); ?>
<? $this->Debug($RESULT['PAGES'], 'PAGES'); ?>

<?
$RESULT['VARIABLE'] = $RESULT['VARIABLE'] ?: 'PAGE';
$get = $_GET;
unset($get[$RESULT['VARIABLE']]);
$base = http_build_query($get);
?>
<div class="pager">

	<div class="alert alert-info float-right p-2">
		<?= T([
			'en' => "Showing <strong>{$RESULT['DATA']['PAGER']['SELECTED']}</strong> elements.",
			'ru' => "Отображено <strong>{$RESULT['DATA']['PAGER']['SELECTED']}</strong> элементов.",
		]) ?>
		<?= T([
			'en' => "Total <strong>{$RESULT['DATA']['PAGER']['TOTAL']}</strong> elements.",
			'ru' => "Всего <strong>{$RESULT['DATA']['PAGER']['TOTAL']}</strong> элементов.",
		]) ?>
	</div>

	<nav>
		<ul class="pagination">
			<? foreach ($RESULT['PAGES'] as $page): ?>
				<? if ($page['ACTIVE']): ?>
					<li class="page-item active">
						<a class="page-link">
							<?= $page["INDEX"] ?>
						</a>
					</li>
				<? elseif ($page['...']): ?>
					<li class="page-item">
						<a
							class="page-link"
							href="javascript:if(page = prompt('<?= T([
								'en' => 'Input page number',
								'ru' => 'Введите номер страницы',
							]) ?>', '')){window.location='?<?= $base ?>&<?= $RESULT['VARIABLE'] ?>='+page}">
							<?= $page["INDEX"] ?>
						</a>
					</li>
				<? else : ?>
					<li class="page-item">
						<a
							class="page-link"
							href="?<?= http_build_query(array_merge($_GET, array($RESULT['VARIABLE'] => $page['INDEX']))) ?>">
							<?= $page["INDEX"] ?>
						</a>
					</li>
				<? endif; ?>
			<? endforeach; ?>
		</ul>
	</nav>

</div>
