<?php /** @var \System\Component $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>
<script src="<?= $this->template_relative_folder ?>/script.js"></script>
<link rel='stylesheet' href="<?= $this->template_relative_folder ?>/style.css"/>

<h2 class="menu-title collapse">Меню</h2>

<? function BuildDefaultMenuRecursive($item) { ?>
	<li data-menu-item="">
		<div class="item <?= $item['ACTIVE'] ? 'active' : '' ?> <?= $item['CURRENT'] ? 'current' : '' ?>">

			<? if ($item['CHILDREN']): ?>
				<i class="menu-point menu-point-category <?= $item['ACTIVE'] ? 'rotate-90' : 'rotate-0' ?>" data-menu-expander=""></i>
			<? else: ?>
				<i class="menu-point menu-point-item"></i>
			<? endif; ?>

			<a
				href="<?= $item['LINK'] ?: '#' ?>"
				<? if ($item['CHILDREN']): ?>
					data-menu-category=""
				<? endif; ?>
			>
				<?= $item['NAME'] ?>
			</a>

		</div>
		<? if ($item['CHILDREN']): ?>
			<ul data-menu-children="" class="<?= $item['ACTIVE'] ? '' : 'collapse' ?>">
				<? foreach ($item['CHILDREN'] as $child): ?>
					<? BuildDefaultMenuRecursive($child); ?>
				<? endforeach; ?>
			</ul>
		<? endif; ?>
	</li>
<? } ?>

<? foreach ($RESULT as $category): ?>
	<h4 class="menu-title"><?= $category['NAME'] ?></h4>
	<ul class="menu" data-menu="">
		<? foreach ($category['CHILDREN'] as $item): ?>
			<? BuildDefaultMenuRecursive($item); ?>
		<? endforeach; ?>
	</ul>
<? endforeach; ?>