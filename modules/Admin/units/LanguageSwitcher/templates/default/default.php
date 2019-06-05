<div class="dropdown d-inline-block">
	<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<?= $RESULT['LANGUAGES'][$RESULT['LANGUAGE']] ?>
	</button>
	<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		<? foreach ($RESULT['LANGUAGES'] as $code => $display): ?>
			<a
				class="dropdown-item"
				href="?<?= http_build_query(array_merge($_GET, ['SwitchLanguage' => $code])) ?>"
			><?= $display ?></a>
		<? endforeach; ?>
	</div>
</div>