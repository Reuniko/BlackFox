<?php /** @var \System\Unit $this */ ?>
<?php /** @var array $RESULT */ ?>
	<script src="<?= $this->template_relative_folder ?>/script.js"></script>
	<link rel='stylesheet' href="<?= $this->template_relative_folder ?>/style.css"/>

	<ul class="menu">
		<? foreach ($RESULT as $category): ?>
			<? BuildDefaultMenuRecursive($category); ?>
		<? endforeach; ?>
	</ul>

<? function BuildDefaultMenuRecursive($item, $level = 1) { ?>
	<li data-menu-item="">
		<div data-menu-item-body="" class="item level-<?= $level ?> <?= $item['ACTIVE'] ? 'active' : '' ?> <?= $item['CURRENT'] ? 'current' : '' ?>">

			<? if ($item['CHILDREN']): ?>
				<i class="menu-point menu-point-category <?= $item['ACTIVE'] ? 'rotate-90' : 'rotate-0' ?>"
				   data-menu-expander=""
				   data-menu-rotate=""
				></i>
			<? else: ?>
				<i class="menu-point menu-point-item"></i>
			<? endif; ?>

			<? if ($item['LINK'] && !$item['EXPANDER']): ?>
				<a href="<?= $item['LINK'] ?: '#' ?>">
					<?= $item['NAME'] ?>
				</a>
			<? else: ?>
				<span data-menu-expander="">
					<?= $item['NAME'] ?>
				</span>
			<? endif; ?>


		</div>
		<? if ($item['CHILDREN']): ?>
			<ul data-menu-children="" class="<?= $item['ACTIVE'] ? '' : 'collapse' ?>">
				<? foreach ($item['CHILDREN'] as $child): ?>
					<? BuildDefaultMenuRecursive($child, $level + 1); ?>
				<? endforeach; ?>
			</ul>
		<? endif; ?>
	</li>
<? } ?>