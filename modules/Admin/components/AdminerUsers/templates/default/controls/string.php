<?php /** @var \Admin\Adminer $this */ ?>
<? if ($code <> 'PASSWORD'): ?>
	<? require $this->TemplateParentPath(); ?>
<? else: ?>
	<input
		type="password"
		class="form-control"
		id="<?= $code ?>"
		name="FIELDS[<?= $code ?>]"
		placeholder=""
		<?= ($field['DISABLED']) ? 'disabled' : '' ?>
	>
<? endif; ?>