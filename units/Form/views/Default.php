<? /** @var \BlackFox\Unit $this */ ?>
<? /** @var array $RESULT */ ?>
<? /** @var \BlackFox\SCRUD $SCRUD */ ?>
<? $SCRUD = $RESULT['SCRUD']; ?>

<? foreach ($RESULT['FIELDS'] as $code => $field): ?>
	<div class="<?= $RESULT['CLASS_GROUP'] ?>">
		<label
			class="<?= $RESULT['CLASS_LABEL'] ?> <?= ($field['NOT_NULL']) ? 'mandatory' : '' ?>"
			for="<?= $RESULT['ELEMENT'] ?>[<?= $code ?>]"
			title="<?= $field['HINT'] ?>"
		>
			<?= $field['NAME'] ?: "{{$code}}" ?>
		</label>
		<div class="<?= $RESULT['CLASS_BLOCK'] ?>">
			<?
			// -------------------------------------------------------------------------------------------
			$SCRUD->fields[$code]->PrintFormControl($RESULT['DATA'][$code], "{$RESULT['ELEMENT']}[{$code}]", $RESULT['CLASS_CONTROL']);
			// -------------------------------------------------------------------------------------------
			?>
		</div>
	</div>
<? endforeach; ?>
