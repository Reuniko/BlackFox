<?php /** @var \System\Component $this */ ?>
<?php /** @var array $RESULT */ ?>

<link rel='stylesheet' href="<?= $this->template_relative_folder ?>/style.css"/>

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

	<? foreach ($RESULT['MENU'] as $category): ?>
		<? BuildDefaultBreadcrumbsRecursive($category); ?>
	<? endforeach; ?>

	<? foreach ($RESULT['BREADCRUMBS'] as $breadcrumb): ?>
		<li class="breadcrumb-item">
			<a href="<?= $breadcrumb['LINK'] ?>"><?= $breadcrumb['NAME'] ?></a>
		</li>
	<? endforeach; ?>
</ol>
