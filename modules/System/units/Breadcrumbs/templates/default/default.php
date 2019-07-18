<?php /** @var \System\Unit $this */ ?>
<?php /** @var array $RESULT */ ?>

<? if (!empty($RESULT['BREADCRUMBS'])): ?>
	<ol class="system breadcrumb">
		<? foreach ($RESULT['BREADCRUMBS'] as $breadcrumb): ?>
			<li class="breadcrumb-item">
				<a href="<?= $breadcrumb['LINK'] ?>"><?= $breadcrumb['NAME'] ?></a>
			</li>
		<? endforeach; ?>
	</ol>
<? endif; ?>
