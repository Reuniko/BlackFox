<? /** @var \System\Unit $this */ ?>
<? /** @var array $RESULT */ ?>
<? $this->Debug($this->PARAMS, 'PAGER'); ?>
<? $this->Debug($RESULT, 'PAGES'); ?>

<?
$get = $_GET;
unset($get[$this->PARAMS['VARIABLE']]);
$base = http_build_query($get);
?>
<div class="pager">

	<div class="alert alert-info float-right m-0 p-2">
		<strong title="<?= T([
			'en' => 'Showing',
			'ru' => 'Отображено',
		]) ?>"><?= $this->PARAMS['SELECTED'] ?></strong>
		/
		<span title="<?= T([
			'en' => 'Total',
			'ru' => 'Всего',
		]) ?>"><?= $this->PARAMS['TOTAL'] ?></span>
	</div>

	<nav class="d-inline-block">
		<ul class="pagination">
			<? foreach ($RESULT as $page): ?>
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
							]) ?>', '')){window.location='?<?= $base ?>&<?= $this->PARAMS['VARIABLE'] ?>='+page}">
							<?= $page["INDEX"] ?>
						</a>
					</li>
				<? else : ?>
					<li class="page-item">
						<a
							class="page-link"
							href="?<?= http_build_query(array_merge($_GET, [$this->PARAMS['VARIABLE'] => $page['INDEX']])) ?>">
							<?= $page["INDEX"] ?>
						</a>
					</li>
				<? endif; ?>
			<? endforeach; ?>
		</ul>
	</nav>

	<div class="clearfix"></div>

</div>
