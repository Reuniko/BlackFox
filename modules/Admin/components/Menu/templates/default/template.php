<?php /** @var \System\Component $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>

<div class="menu list-group" data-menu="">
	<? foreach ($RESULT as $id1 => $element1): ?>
		<a
			class="list-group-item level1 p-2 <?= $element1['ACTIVE'] ? 'active' : '' ?>"
			href="<?= $element1['LINK'] ?: '#' ?>"
			<? if ($element1['CHILDREN']): ?>
				data-menu-category=""
			<? endif; ?>
		>
			<span><?= $element1['NAME'] ?></span>
		</a>
		<? if ($element1['CHILDREN']): ?>
			<div data-menu-children="" class="<?= $element1['ACTIVE'] ? '' : 'collapse' ?>">
				<? foreach ($element1['CHILDREN'] as $id2 => $element2): ?>
					<a
						class="list-group-item level2 p-1 pl-3 <?= $element2['ACTIVE'] ? 'active' : '' ?>"
						href="<?= $element2['LINK'] ?>"
					><span><?= $element2['NAME'] ?></span></a>
				<? endforeach; ?>
			</div>
		<? endif; ?>
	<? endforeach; ?>
</div>

<script><?include('script.js')?></script>
<style><?include('style.css')?></style>
