<?php /** @var \System\Component $this */ ?>
<?php $this->Debug($RESULT, '$RESULT'); ?>
<div class="well col-sm-6">
	<form class="form" method="POST">
		<h2>Вход в систему</h2>
		<? $this->ShowMessages(); ?>
		<input type="text" name="login" value="<?= $RESULT['LOGIN'] ?>" class="form-control" placeholder="Логин"/>
		<input type="password" name="password" value="<?= $RESULT['PASSWORD'] ?>" class="form-control" placeholder="Пароль"/>
		<button type="submit" class="btn btn-default" name="ACTION" value="Login">
			Войти
		</button>
	</form>

</div>