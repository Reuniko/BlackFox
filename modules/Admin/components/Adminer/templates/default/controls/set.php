<input
	type="hidden"
	name="FIELDS[<?= $code ?>]"
	value=""
/>
<? foreach ($field['VALUES'] as $value => $display): ?>
	<div>
		<label class="enum">
			<input
				type="checkbox"
				class=""
				name="FIELDS[<?= $code ?>][]"
				value="<?= $value ?>"
				<?= (in_array($value, $RESULT['DATA'][$code] ?: [])) ? 'checked' : '' ?>
				<?= ($field['DISABLED']) ? 'disabled' : '' ?>
			>
			<span><?= $display ?></span>
		</label>
	</div>
<? endforeach; ?>