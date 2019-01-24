<?php

namespace System;

class TypeOuter extends Type {
	public static $TYPE = 'OUTER';

	public function GetStructureStringType() {
		return 'int';
	}

	public function FormatInputValue($value) {
		return (int)$value;
	}

	public function FormatOutputValue($element) {
		/** @var SCRUD $Link */
		$Link = $this->info['LINK'];
		$code = $this->info['CODE'];
		if (!in_array('System\SCRUD', class_parents($Link))) {
			throw new ExceptionType("Field '{$code}': link '{$Link}' must be SCRUD child ");
		}
		$element[$code] = $Link::I()->FormatOutputValues($element[$code]);
		return $element;
	}

	public function PrepareSelectAndJoinByField($table, $prefix, $subfields) {
		if (empty($subfields)) {
			return parent::PrepareSelectAndJoinByField($table, $prefix, null);
		}
		$code = $this->info['CODE'];
		/** @var SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$external_prefix = $prefix . $code . "__";
		$raw_link_code = $external_prefix . $Link->code;

		$RESULT = $Link->PrepareSelectAndJoinByFields($subfields, $external_prefix);
		$join = "LEFT JOIN {$Link->code} AS {$raw_link_code} ON {$prefix}{$table}.{$code} = {$raw_link_code}.{$Link->key()}";
		$RESULT['JOIN'] = array_merge(
			[$raw_link_code => $join],
			$RESULT['JOIN']
		);
		return $RESULT;
	}

	public function GenerateJoinStatements(SCRUD $Current, $prefix) {
		// debug($this->info, '$this->info');
		/** @var SCRUD $Target */
		$Target = $this->info['LINK']::I();

		$current_alias = $prefix . $Current->code;
		$current_key = $this->info['CODE'];
		$target_alias = $prefix . $this->info['CODE'] . '__' . $Target->code;
		$target_key = $Target->key();

		$statement = "LEFT JOIN {$Target->code} AS {$target_alias} ON {$current_alias}.{$current_key} = {$target_alias}.{$target_key}";
		return [$target_alias => $statement];
	}

	public function PrintValue($value) {
		/** @var \System\SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$url = $Link->GetAdminUrl();
		$ID = is_array($value) ? $value['ID'] : $value;
		?>
		<? if (User::I()->InGroup('root')): ?>
			<nobr>[<a target="_top" href="<?= $url ?>?ID=<?= $ID ?>"><?= $ID ?></a>]</nobr>
		<? endif; ?>
		<?= $Link->GetElementTitle($value); ?>
		<?
	}

	public function PrintFormControl($value, $name, $class = 'form-control') {
		// TODO ajax select
		/** @var \System\SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$url = $Link->GetAdminUrl();
		$code = $this->info['CODE'];
		$ID = is_array($value) ? $value['ID'] : $value;
		?>

		<div class="row">
			<div class="col-12 col-sm-6 mb-1">
				<div class="input-group">
					<div class="input-group-prepend">
						<button
							style="width: 40px;"
							type="button"
							class="btn btn-secondary"
							title="Выбрать элемент"
							<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
							onclick="window.open(
								'<?= $url ?>?popup=<?= $name ?>',
								'',
								'height=' + ((screen.height) - 100) + ',width=' + ((screen.width) - 20) + ''
								);"
						>
							<i class="fa fa-search"></i>
						</button>
					</div>
					<input
						type="text"
						class="<?= $class ?>"
						width="100px"
						id="<?= $name ?>"
						name="<?= $name ?>"
						data-link-input="<?= $name ?>"
						value="<?= $ID ?>"
						<?= ($this->info['DISABLED']) ? 'disabled' : '' ?>
					>
				</div>
			</div>
			<div class="col-12 col-sm-6 mb-1">
				<div class="input-group">
					<? if (!isset($_REQUEST['FRAME'])): ?>
						<div class="input-group-prepend">
							<a
								style="width: 40px;"
								class="btn btn-secondary"
								href="<?= ($ID) ? "{$url}?ID={$ID}" : "" ?>"
								data-link-a="<?= $name ?>"
								title="Открыть элемент"
							><i class="fa fa-external-link-alt"></i></a>
						</div>
					<? endif; ?>
					<input
						type="text"
						class="<?= $class ?>"
						disabled="disabled"
						data-link-span="<?= $name ?>"
						value="<?= is_array($value) ? $Link->GetElementTitle($value) : '' ?>"
					>
				</div>
			</div>
		</div>
		<?
	}

	public function PrintFilterControl($filter, $group = 'FILTER', $class = 'form-control') {
		/** @var \System\SCRUD $Link */
		$Link = $this->info['LINK']::I();
		$code = $this->info['CODE'];
		$url = $Link->GetAdminUrl();
		$ID = $filter[$code];
		?>
		<div class="btn-toolbar" style="vertical-align: middle; line-height: 34px;">
			<div class="btn-group">
				<button
					type="button"
					class="form-control"
					onclick="window.open(
						'<?= $url ?>?popup=FILTER[<?= $code ?>]',
						'',
						'height=' + ((screen.height) - 100) + ',width=' + ((screen.width) - 20) + ''
						);"
				>
					<i class="fa fa-search"></i>
				</button>
			</div>
			<div class="btn-group">
				<input
					type="text"
					class="<?= $class ?>"
					id="<?= $group ?>[<?= $code ?>]"
					name="<?= $group ?>[<?= $code ?>]"
					data-link-input="<?= $group ?>[<?= $code ?>]"
					value="<?= $ID ?>"
				>
			</div>
		</div>
		<?
	}
}