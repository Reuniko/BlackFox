<select
	class="form-control"
	name="FILTER[<?= $code ?>]"
>
	<option value="">- не фильтровать -</option>
	<option value="0" <?= ($RESULT['FILTER'][$code] === '0') ? 'selected' : '' ?>>Нет</option>
	<option value="1" <?= ($RESULT['FILTER'][$code] === '1') ? 'selected' : '' ?>>Да</option>
</select>
