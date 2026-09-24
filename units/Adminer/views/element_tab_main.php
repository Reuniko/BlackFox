<?php /** @var \BlackFox\Adminer $this */ ?>
<?php /** @var array $RESULT */ ?>

<? if ($path = $this->PathInclude('element_actions.php')) include($path); ?>

<form method="post" enctype="multipart/form-data" class="form-horizontal">

	<input type="hidden" name="ACTION" value="<?= $RESULT['MODE'] ?>"/>

	<? if ($path = $this->PathInclude('element_head.php')) include($path); ?>

	<? foreach ($this->SCRUD->composition as $group_code => $group): ?>
		<? if (!empty($group['FIELDS'])): ?>
			<? if (count($this->SCRUD->composition) > 1): ?>
				<h3 class="group_header" title="<?= $group_code ?>"><?= $group['NAME'] ?></h3>
			<? endif; ?>
		<? endif; ?>
		<? foreach ($group['FIELDS'] as $code => $field): ?>
			<div class="form-group row">
				<label
					class="col-sm-3 col-form-label text-sm-right <?= ($field['NOT_NULL']) ? 'mandatory' : '' ?> <?= ($field['VITAL']) ? 'vital' : '' ?>"
					for="FIELDS[<?= $code ?>]"
					title="<?= $code ?>"
				>
					<?= $field['NAME'] ?: "{{$code}}" ?>
				</label>
				<div class="col-sm-8">
					<?
					// -------------------------------------------------------------------------------------------
					$this->SCRUD->Types[$code]->PrintFormControl($RESULT['DATA'][$code], "FIELDS[{$code}]");
					// -------------------------------------------------------------------------------------------
					?>
				</div>
				<div class="col-sm-1 col-form-label d-none d-sm-inline-block">
					<? if ($field['DESCRIPTION']): ?>
						<span
							class="material-icons"
							title="<?= $field['DESCRIPTION'] ?>"
							data-toggle="tooltip"
						>info</span>
					<? endif; ?>
				</div>
			</div>
		<? endforeach; ?>
	<? endforeach; ?>

	<? if ($path = $this->PathInclude('element_foot.php')) include($path); ?>

	<hr/>

	<div class="buttons">
		<? if ($path = $this->PathInclude('element_bottom_buttons.php')) include($path); ?>
	</div>
</form>