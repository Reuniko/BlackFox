<div class="row no-gutters">
	<div class="col-6">
		<input
			type="text"
			class="form-control"
			id="<?= $code ?>"
			name="FILTER[><?= $code ?>]"
			placeholder="от"
			value="<?= $RESULT['FILTER']['>' . $code] ?>"
			data-datepicker=""
		>
	</div>
	<div class="col-6">
		<input
			type="text"
			class="form-control"
			id="<?= $code ?>"
			name="FILTER[<<?= $code ?>]"
			placeholder="до"
			value="<?= $RESULT['FILTER']['<' . $code] ?>"
			data-datepicker=""
		>
	</div>
</div>