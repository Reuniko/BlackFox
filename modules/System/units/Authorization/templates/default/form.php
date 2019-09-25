<?php /** @var \System\Unit $this */ ?>
<div class="authorization">
	<h1 class="text-center my-3"><?= $this->PARAMS['TITLE'] ?></h1>
	<div
		class="
			col-sm-8 offset-sm-2
			col-md-6 offset-md-3
			"
	>
		<? $this->ShowAlerts(); ?>
		<form class="form card p-3" method="POST">
			<div class="form-group">
				<input
					type="text"
					name="login"
					value="<?= $RESULT['LOGIN'] ?>"
					class="form-control"
					placeholder="<?= T([
						'en' => 'Login',
						'ru' => 'Логин',
					]) ?>"
					<? if (empty($RESULT['LOGIN'])): ?>
						autofocus="autofocus"
					<? endif; ?>
				/>
			</div>
			<div class="form-group">
				<input
					type="password"
					name="password"
					class="form-control"
					placeholder="<?= T([
						'en' => 'Password',
						'ru' => 'Пароль',
					]) ?>"
					<? if (!empty($RESULT['LOGIN'])): ?>
						autofocus="autofocus"
					<? endif; ?>
				/>
			</div>

			<? if ($this->PARAMS['CAPTCHA']): ?>
				<div class="form-group text-center">
					<? \System\Captcha::I()->Show(['CSS_CLASS' => 'd-inline-block']) ?>
				</div>
			<? endif; ?>

			<div class="form-group text-center mb-0">
				<button type="submit" class="btn btn-primary" name="ACTION" value="Login">
					<i class="fa fa-door-open"></i>
					<?= T([
						'en' => 'Sing in',
						'ru' => 'Войти',
					]) ?>
				</button>
			</div>
		</form>
	</div>

	<? if ($this->PARAMS['REGISTRATION']): ?>
		<div class="form-group text-center">
			<a class="btn btn-link" href="<?= $this->PARAMS['REGISTRATION'] ?>"><?= T([
					'en' => 'Registration',
					'ru' => 'Регистрация',
				]) ?></a>
		</div>
	<? endif; ?>
</div>