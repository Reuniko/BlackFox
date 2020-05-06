<?php /** @var \BlackFox\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>
<?php $this->Debug($this->SCRUD->composition, 'composition'); ?>
<?
if ($RESULT['MODE'] === 'Create') {
	$this->ENGINE->TITLE = T([
		'en' => "Adding element of '{$this->SCRUD->name}'",
		'ru' => "Добавление элемента '{$this->SCRUD->name}'",
	]);
} else {
	$this->ENGINE->TITLE = T([
		'en' => "Editing element #{$RESULT['DATA']['ID']} of '{$this->SCRUD->name}'",
		'ru' => "Редактирование элемента №{$RESULT['DATA']['ID']} '{$this->SCRUD->name}'",
	]);
}
?>

<div class="adminer">

	<!-- Nav tabs -->
	<? if (count($RESULT['TABS']) > 1): ?>
		<ul class="nav nav-tabs" id="tabs" role="tablist">
			<? foreach ($RESULT['TABS'] as $tab_code => $tab): ?>
				<li class="nav-item">
					<a
						class="nav-link <?= ($tab['ACTIVE'] ? 'active' : '') ?>"
						data-toggle="tab"
						href="#<?= strtolower($tab_code) ?>"
					><?= $tab['NAME'] ?></a>
				</li>
			<? endforeach; ?>
		</ul>
	<? endif; ?>

	<!-- Tab panes -->
	<div class="tab-content">
		<? foreach ($RESULT['TABS'] as $tab_code => $tab): ?>
			<div
				class="tab-pane tab-element <?= ($tab['ACTIVE'] ? 'active' : '') ?>"
				id="<?= strtolower($tab_code) ?>"
				role="tabpanel"
				aria-labelledby="<?= strtolower($tab_code) ?>-tab"
			>
				<?
				$RESULT['TAB'] = $tab;
				$RESULT['TAB']['CODE'] = $tab_code;
				require($this->Path("{$tab['VIEW']}.php"));
				?>
			</div>
		<? endforeach; ?>
	</div>

</div>