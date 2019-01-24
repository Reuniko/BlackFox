<?php /** @var \Admin\Adminer $this */ ?>
<form method="post" enctype="multipart/form-data" class="form-horizontal">

	<input type="hidden" name="ACTION" value="<?= $RESULT['MODE'] ?>"/>

	<? @include($this->PathInclude('element_head.php')); ?>

	<? foreach ($this->SCRUD->composition as $group_code => $group): ?>
		<? if (!empty($group['FIELDS'])): ?>
			<? if (count($this->SCRUD->composition) > 1): ?>
				<h3 class="group_header" title="<?= $group_code ?>"><?= $group['NAME'] ?></h3>
			<? endif; ?>
		<? endif; ?>
		<? foreach ($group['FIELDS'] as $code => $field): ?>
			<div class="form-group row">
				<label
					class="col-sm-3 col-form-label text-sm-right <?= ($field['NOT_NULL']) ? 'mandatory' : '' ?>"
					for="FIELDS[<?= $code ?>]"
					title="<?= $code ?>"
				>
					<?= $field['NAME'] ?: "{{$code}}" ?>
				</label>
				<div class="col-sm-8">
					<?
					// -------------------------------------------------------------------------------------------
					$this->SCRUD->structure[$code]->PrintFormControl($RESULT['DATA'][$code], "FIELDS[{$code}]");
					// -------------------------------------------------------------------------------------------
					?>
				</div>
				<div class="col-sm-1 col-form-label d-none d-sm-inline-block">
					<? if ($field['DESCRIPTION']): ?>
						<i
							class="far fa-question-circle"
							title="<?= $field['DESCRIPTION'] ?>"
							data-toggle="tooltip"
						></i>
					<? endif; ?>
				</div>
			</div>
		<? endforeach; ?>
	<? endforeach; ?>

	<? @include($this->PathInclude('element_foot.php')); ?>

	<hr/>

	<div class="buttons">
		<? @include($this->PathInclude('element_bottom_buttons.php')); ?>
	</div>
</form>