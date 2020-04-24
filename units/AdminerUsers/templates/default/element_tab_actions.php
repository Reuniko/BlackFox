<div class="form-group row">
	<label class="col-sm-3 col-form-label text-right"></label>
	<div class="col-sm-8">
		<form method="post">
			<button
				type="submit"
				name="ACTION"
				value="Login"
				class="btn btn-info"
			>
				<?= T([
					'en' => 'Log in with this user',
					'ru' => 'Авторизоваться под этим пользователем',
				]) ?>
			</button>
		</form>
	</div>
</div>