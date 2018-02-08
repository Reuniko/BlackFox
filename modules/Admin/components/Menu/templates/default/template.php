<?php /** @var \System\Component $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>
<script src="<?= $this->template_relative_folder ?>/script.js"></script>
<link rel='stylesheet' href="<?= $this->template_relative_folder ?>/style.css"/>

<h2 class="menu-title">Меню</h2>

<? function BuildDefaultMenuRecursive($item) { ?>
	<li>
		<a
			class="
					item
					<?= $item['ACTIVE'] ? 'active' : '' ?>
					<?= $item['CURRENT'] ? 'current' : '' ?>
					"
			href="<?= $item['LINK'] ?: '#' ?>"
			<? if ($item['CHILDREN']): ?>
				data-menu-category=""
			<? endif; ?>
		>
				<span>
					<? if ($item['CHILDREN']): ?>
						<i class="icon icon-category <?= $item['ACTIVE'] ? 'fa-rotate-90' : '' ?>" data-menu-rotator=""></i>
					<? else: ?>
						<i class="icon icon-item"></i>
					<? endif; ?>
					<?= $item['NAME'] ?>
				</span>
		</a>
		<? if ($item['CHILDREN']): ?>
			<ul data-menu-children="" class="<?= $item['ACTIVE'] ? '' : 'collapse' ?>">
				<? foreach ($item['CHILDREN'] as $child): ?>
					<? BuildDefaultMenuRecursive($child); ?>
				<? endforeach; ?>
			</ul>
		<? endif; ?>
	</li>
<? } ?>

<ul class="menu" data-menu="">
	<? foreach ($RESULT as $item): ?>
		<? BuildDefaultMenuRecursive($item); ?>
	<? endforeach; ?>
</ul>