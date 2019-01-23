<?php /** @var \System\Unit $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>

<div class="row">
	<div
		class="
			col-sm-8 offset-sm-2
			col-md-6 offset-md-3
			"
	>
		<? $this->ShowAlerts(); ?>
		<form class="form card p-3" method="POST">
			<h2><?= $this->PARAMS['TITLE'] ?: 'Вход в систему' ?></h2>
			<div class="form-group">
				<input
					type="text"
					name="login"
					value="<?= $RESULT['LOGIN'] ?>"
					class="form-control"
					placeholder="Логин"
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
					placeholder="Пароль"
					<? if (!empty($RESULT['LOGIN'])): ?>
						autofocus="autofocus"
					<? endif; ?>
				/>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary" name="ACTION" value="Login">
					Войти
				</button>
			</div>
		</form>
	</div>
</div>