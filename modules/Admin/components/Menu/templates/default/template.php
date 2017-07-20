<?php /** @var \System\Component $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>

<ul class="nav nav-sidebar">
	<? foreach ($RESULT as $element): ?>
		<li class="<?= $element['ACTIVE'] ? 'active' : '' ?>">
			<a href="<?= $element['LINK'] ?>">
				<?= $element['NAME'] ?>
			</a>
		</li>
	<? endforeach; ?>
</ul>
