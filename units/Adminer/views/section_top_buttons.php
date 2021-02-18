<div class="my-2 buttons">

	<a
		class="btn btn-light float-right"
		data-toggle="modal"
		data-target="#section-settings"
	>
		<span class="material-icons">settings</span>
		<?= T([
			'en' => 'Settings',
			'ru' => 'Настройки',
		]) ?>
	</a>

	<? if (in_array($RESULT['MODE'], ['SECTION'])): ?>
		<a class="btn btn-success" href="?NEW&<?= http_build_query($_GET) ?>">
			<span class="material-icons">add</span>
			<?= T([
				'en' => 'Add',
				'ru' => 'Создать',
			]) ?>
		</a>
	<? endif; ?>

	<div class="clearfix"></div>

</div>