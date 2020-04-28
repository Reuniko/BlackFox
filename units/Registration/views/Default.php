<? /** @var \BlackFox\Registration $this */ ?>
<? /** @var array $RESULT */ ?>
<div class="registration">
	<h1 class="my-3 text-center"><?= $this->PARAMS['TITLE'] ?></h1>
	<? $this->ShowAlerts(); ?>
	<form method="POST" class="form form-horizontal" enctype="multipart/form-data">
		<? foreach ($RESULT['FIELDS'] as $code => $field): ?>
			<? if ($field->info['TYPE'] === 'BOOL'): ?>
				<div class="form-group text-center">
					<?
					// -------------------------------------------------------------------------------------------
					\BlackFox\Users::I()->fields[$code]->PrintFormControl($RESULT['VALUES'][$code], "VALUES[{$code}]");
					// -------------------------------------------------------------------------------------------
					?>
					<label for="VALUES[<?= $code ?>]" class="m-0">
						<?= $field['NAME'] ?>
					</label>
				</div>
			<? else: ?>
				<div class="form-group row">
					<label for="VALUES[<?= $code ?>]" class="col-sm-3 form-control-plaintext text-sm-right">
						<?= $field['NAME'] ?>
						<? if (in_array($code, $this->PARAMS['MANDATORY'])): ?>
							<span class="red">*</span>
						<? endif; ?>
					</label>
					<div class="col-sm-8">
						<?
						// -------------------------------------------------------------------------------------------
						\BlackFox\Users::I()->fields[$code]->PrintFormControl($RESULT['VALUES'][$code], "VALUES[{$code}]");
						// -------------------------------------------------------------------------------------------
						?>
					</div>
					<? if (\BlackFox\Users::I()->fields[$code]['DESCRIPTION']): ?>
						<div class="col-sm-1 form-control-plaintext sm-hidden">
							<i
								class="fa fa-question"
								title="<?= \BlackFox\Users::I()->fields[$code]['DESCRIPTION'] ?>"
							></i>
						</div>
					<? endif; ?>
				</div>
			<? endif; ?>
		<? endforeach; ?>

		<? if ($this->PARAMS['CAPTCHA']): ?>
			<div class="form-group text-center">
				<? \BlackFox\Captcha::I()->Show(['CSS_CLASS' => 'd-inline-block']) ?>
			</div>
		<? endif; ?>

		<div class="form-group text-center">

			<button class="btn btn-primary" type="submit" name="ACTION" value="Registration">
				<i class="fa fa-user-plus"></i>
				<?= T([
					'en' => 'Sign up',
					'ru' => 'Зарегистрироваться',
				]) ?>
			</button>

		</div>

		<? if ($this->PARAMS['AUTHORIZATION']): ?>
			<hr/>
			<div class="form-group text-center">
				<a class="btn btn-link" href="<?= $this->PARAMS['AUTHORIZATION'] ?>"><?= T([
						'en' => 'Authorization',
						'ru' => 'Авторизация',
					]) ?></a>
			</div>
		<? endif; ?>


	</form>
</div>