<?php /** @var \System\Unit $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>
<div class="row">
	<div
		style="margin-top: 200px;"
		class="
			well
			col-sm-8 col-sm-offset-2
			col-md-6 col-md-offset-3
			"
	>
		<form class="form" method="POST">
			<? $this->ShowAlerts(); ?>
			<h2><?= $this->PARAMS['TITLE'] ?: 'Вход в систему' ?></h2>
			<div class="form-group">
				<input type="text" name="login" value="<?= $RESULT['LOGIN'] ?>" class="form-control" placeholder="Логин"/>
			</div>
			<div class="form-group">
				<input type="password" name="password" value="<?= $RESULT['PASSWORD'] ?>" class="form-control" placeholder="Пароль"/>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary" name="ACTION" value="Login">
					Войти
				</button>
			</div>
		</form>
	</div>
</div>