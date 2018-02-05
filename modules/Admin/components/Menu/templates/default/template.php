<?php /** @var \System\Component $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>

<div class="menu list-group">
	<? foreach ($RESULT as $element): ?>
		<a
			class="list-group-item p-2 <?= $element['ACTIVE'] ? 'active' : '' ?>"
			href="<?= $element['LINK'] ?>"
		>
			<?= $element['NAME'] ?>
		</a>
	<? endforeach; ?>
</div>
