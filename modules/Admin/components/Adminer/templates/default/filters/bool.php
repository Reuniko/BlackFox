<select
	class="form-control"
	name="FILTER[<?= $code ?>]"
	>
	<option value="">- не фильтровать -</option>
	<option value="Y" <?= ($RESULT['FILTER'][$code] === 'Y') ? 'selected' : '' ?>>Да</option>
	<option value="N" <?= ($RESULT['FILTER'][$code] === 'N') ? 'selected' : '' ?>>Нет</option>
</select>
