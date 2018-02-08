<?php /** @var \System\Component $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>
<script src="<?= $this->template_relative_folder ?>/script.js"></script>
<link rel='stylesheet' href="<?= $this->template_relative_folder ?>/style.css"/>

<h2 class="menu-title">Меню</h2>

<ul class="menu" data-menu="">
	<? foreach ($RESULT as $id1 => $element1): ?>
		<li>
			<a
				class="level1 <?= $element1['ACTIVE'] ? 'active' : '' ?>"
				href="<?= $element1['LINK'] ?: '#' ?>"
				<? if ($element1['CHILDREN']): ?>
					data-menu-category=""
				<? endif; ?>
			>
				<span>
					<? if ($element1['CHILDREN']): ?>
						<i class="icon icon-category <?= $element1['ACTIVE'] ? 'fa-rotate-90' : '' ?>" data-menu-rotator=""></i>
					<? else: ?>
						<i class="icon icon-item"></i>
					<? endif; ?>
					<?= $element1['NAME'] ?>
				</span>
			</a>
			<? if ($element1['CHILDREN']): ?>
				<ul data-menu-children="" class="<?= $element1['ACTIVE'] ? '' : 'collapse' ?>">
					<? foreach ($element1['CHILDREN'] as $id2 => $element2): ?>
						<li>
							<a
								class="level2 <?= $element2['ACTIVE'] ? 'active' : '' ?>"
								href="<?= $element2['LINK'] ?>"
							>
								<span>
									<i class="icon icon-item"></i>
									<?= $element2['NAME'] ?>
								</span>
							</a>
						</li>
					<? endforeach; ?>
				</ul>
			<? endif; ?>
		</li>
	<? endforeach; ?>
</ul>


