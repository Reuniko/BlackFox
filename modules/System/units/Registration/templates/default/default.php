<? /** @var \System\Registration $this */ ?>
<? /** @var array $RESULT */ ?>
<div class="container registration">
	<? $this->ShowAlerts(); ?>
	<h2><?= $this->PARAMS['TITLE'] ?></h2>
	<form method="POST" class="form form-horizontal" enctype="multipart/form-data">
		<? foreach ($RESULT['FIELDS'] as $code => $field): ?>
			<div class="form-group row">
				<label for="<?= $code ?>" class="col-sm-3 form-control-plaintext text-sm-right">
					<?= $field['NAME'] ?>
					<? if (in_array($code, $this->PARAMS['MANDATORY'])): ?>
						<span class="red">*</span>
					<? endif; ?>
				</label>
				<div class="col-sm-8">
					<?
					// -------------------------------------------------------------------------------------------
					\System\Users::I()->structure[$code]->PrintFormControl($RESULT['VALUES'][$code], "VALUES[{$code}]");
					// -------------------------------------------------------------------------------------------
					?>
					<? /*
					<input
						type="<?= ($code === 'PASSWORD') ? 'password' : 'text' ?>"
						id="VALUES[<?= $code ?>]"
						name="VALUES[<?= $code ?>]"
						class="form-control"
						~placeholder="<?= $field['NAME'] ?>"
						value="<?= $RESULT['VALUES'][$code] ?>"
					/>
 					*/ ?>
				</div>
			</div>
		<? endforeach; ?>

		<? if ($this->PARAMS['CAPTCHA']): ?>
			<div class="form-group row">
				<div class="offset-sm-3 col-sm-8">
					<? \System\Captcha::I()->Show() ?>
				</div>
			</div>
		<? endif; ?>


		<div class="form-group row">
			<div class="offset-sm-3 col-sm-8">
				<button class="btn btn-primary" type="submit" name="ACTION" value="Registration">
					<i class="fa fa-user-plus"></i>
					<?= T([
						'en' => 'Registration',
						'ru' => 'Регистрация',
					]) ?>
				</button>
			</div>
		</div>
	</form>
</div>