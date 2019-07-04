<?php /** @var \System\Unit $this */ ?>
<div class="row">
	<div
		class="
			col-sm-8 offset-sm-2
			col-md-6 offset-md-3
			"
	>
		<? $this->ShowAlerts(); ?>
		<form class="form card p-3" method="POST">
			<h2><?= $this->PARAMS['TITLE'] ?></h2>
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
			<div class="form-group">
				<button type="submit" class="btn btn-primary" name="ACTION" value="Login">
					<?= T([
						'en' => 'Login',
						'ru' => 'Войти',
					]) ?>
				</button>
			</div>
		</form>
	</div>
</div>