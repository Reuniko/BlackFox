<div class="registration">
	<h2><?= $this->PARAMS['TITLE'] ?></h2>
	<form method="POST" class="form form-horizontal">
		<? foreach ($RESULT['FIELDS'] as $code => $field): ?>
			<div class="form-group row">
				<label for="<?= $code ?>" class="col-sm-3 form-control-plaintext text-sm-right">
					<?= $field['NAME'] ?>
				</label>
				<div class="col-sm-8">
					<input
						type="<?= ($code === 'PASSWORD') ? 'password' : 'text' ?>"
						id="VALUES[<?= $code ?>]"
						name="VALUES[<?= $code ?>]"
						class="form-control"
						~placeholder="<?= $field['NAME'] ?>"
						value="<?= $RESULT['VALUES'][$code] ?>"
					/>
				</div>
			</div>
		<? endforeach; ?>
		<div class="form-group row">
			<label class="control-label col-sm-3">
			</label>

			<div class="col-sm-8">
				<button class="btn btn-primary" type="submit" name="ACTION" value="Registration">
					<?= T([
						'en' => 'Registration',
						'ru' => 'Регистрация',
					]) ?>
				</button>
			</div>
		</div>
	</form>
</div>