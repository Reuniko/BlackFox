<?php /** @var \System\Component $this */ ?>
<?php /** @var array $RESULT */ ?>

<ol class="breadcrumb">
	<? function BuildDefaultBreadcrumbsRecursive($item) { ?>
		<? if ($item['ACTIVE']): ?>
			<li class="breadcrumb-item">
				<? if ($item['LINK']): ?>
					<a href="<?= $item['LINK'] ?>"><?= $item['NAME'] ?></a>
				<? else: ?>
					<?= $item['NAME'] ?>
				<? endif; ?>
			</li>
		<? endif; ?>

		<? if ($item['CHILDREN']): ?>
			<? foreach ($item['CHILDREN'] as $child): ?>
				<? BuildDefaultBreadcrumbsRecursive($child); ?>
			<? endforeach; ?>
		<? endif; ?>
	<? } ?>
	<? foreach ($RESULT as $category): ?>
		<? BuildDefaultBreadcrumbsRecursive($category); ?>
	<? endforeach; ?>
</ol>
