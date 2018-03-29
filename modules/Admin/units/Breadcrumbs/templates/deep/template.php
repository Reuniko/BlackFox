<?php /** @var \System\Unit $this */ ?>
<?php /** @var array $RESULT */ ?>

<div class="breadcrumbs mb-2">

	<? function BuildDefaultBreadcrumbsRecursive($item) { ?>
		<? debug($item) ?>

		<? if ($item['ACTIVE']): ?>
			<div class="btn-group">
				<? if ($item['LINK']): ?>
					<a href="<?= $item['LINK'] ?>" class="btn btn-secondary"><?= $item['NAME'] ?></a>
				<? else: ?>
					<button type="button" class="btn btn-secondary"><?= $item['NAME'] ?></button>
				<? endif; ?>
				<? if ($item['CHILDREN']): ?>
					<button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu">
						<? foreach ($item['CHILDREN'] as $child): ?>
							<? if ($child['LINK']): ?>
								<a href="<?= $child['LINK'] ?>" class="dropdown-item"><?= $child['NAME'] ?></a>
							<? else: ?>
								<button type="button" class="dropdown-item"><?= $child['NAME'] ?></button>
							<? endif; ?>
						<? endforeach; ?>
					</div>
				<? endif; ?>
			</div>

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
</div>
