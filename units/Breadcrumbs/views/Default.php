<?php /** @var \BlackFox\Unit $this */ ?>
<?php /** @var array $RESULT */ ?>

<? if (!empty($RESULT['BREADCRUMBS'])): ?>
	<ol class="system breadcrumb">
		<? foreach ($RESULT['BREADCRUMBS'] as $breadcrumb): ?>
			<li class="breadcrumb-item">
				<? if ($breadcrumb['LINK']): ?>
					<a href="<?= $breadcrumb['LINK'] ?>"><?= $breadcrumb['NAME'] ?></a>
				<? else: ?>
					<span><?= $breadcrumb['NAME'] ?></span>
				<? endif; ?>
			</li>
		<? endforeach; ?>
	</ol>
<? endif; ?>
