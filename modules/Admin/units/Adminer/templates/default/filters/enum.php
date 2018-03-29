<input
	type="hidden"
	name="FILTER[<?= $code ?>]"
	value=""
/>
<? foreach ($field['VALUES'] as $value => $display): ?>
	<div class="col-xs-3">
		<label class="enum">
			<input
				type="checkbox"
				class=""
				name="FILTER[<?= $code ?>][]"
				value="<?= $value ?>"
				<?= (in_array($value, $RESULT['FILTER'][$code] ?: [])) ? 'checked' : '' ?>
				<?= ($field['DISABLED']) ? 'disabled' : '' ?>
			>
			<span class="dashed"><?= $display ?></span>
		</label>
	</div>
<? endforeach; ?>