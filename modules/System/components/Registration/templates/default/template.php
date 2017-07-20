<div class="registration">
	<h2><?= $this->PARAMS['TITLE'] ?></h2>
	<form method="POST" class="form form-horizontal">
		<? foreach ($RESULT['FIELDS'] as $code => $field): ?>
			<div class="form-group">
				<label for="<?= $code ?>" class="control-label col-sm-3">
					<?= $field['NAME'] ?>
				</label>
				<div class="col-sm-9">
					<input
						type="<?= ($code === 'PASSWORD') ? 'password' : 'text' ?>"
						id="VALUES[<?= $code ?>]"
						name="VALUES[<?= $code ?>]"
						class="form-control"
						placeholder="<?= $field['NAME'] ?>"
						value="<?= $RESULT['VALUES'][$code] ?>"
					/>
				</div>
			</div>
		<? endforeach; ?>
		<div class="form-group">
			<label class="control-label col-sm-3">
			</label>

			<div class="col-sm-9">
				<button class="btn btn-default" type="submit" name="ACTION" value="Registration">
					Регистрация
				</button>
			</div>
		</div>
	</form>
</div>