<div class="row">
	<div class="col-xs-6">
		<input
			type="text"
			class="form-control"
			id="<?= $code ?>"
			name="FILTER[><?= $code ?>]"
			placeholder="от"
			value="<?= $RESULT['FILTER']['>' . $code] ?>"
		>
	</div>
	<div class="col-xs-6">
		<input
			type="text"
			class="form-control"
			id="<?= $code ?>"
			name="FILTER[<<?= $code ?>]"
			placeholder="до"
			value="<?= $RESULT['FILTER']['<' . $code] ?>"
		>
	</div>
</div>