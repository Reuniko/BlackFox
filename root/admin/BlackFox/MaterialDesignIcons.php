<?php
/** @var $this \BlackFox\Engine */
$icons = require($this->GetAbsolutePath($this->TEMPLATE_PATH) . '/icons.php');
debug($icons, '$icons');
$this->TITLE = 'Material Design Icons';
$selected_type = (string)$_REQUEST['TYPE'];
$types = [
	'',
	'outlined',
	'round',
	'sharp',
	'two-tone',
];
if (!in_array($selected_type, $types)) $selected_type = '';
?>
<div class="btn-group">
	<? foreach ($types as $type): ?>
		<a
			class="btn btn-<?= $selected_type === $type ? 'primary' : 'secondary' ?>"
			href="?TYPE=<?= $type ?>"
		><?= $type ?: '- default -' ?></a>
	<? endforeach; ?>
</div>


<script type="application/javascript">
    $.fn.selectText = function () {
        this.find('input').each(function () {
            if ($(this).prev().length == 0 || !$(this).prev().hasClass('p_copy')) {
                $('<p class="p_copy" style="position: absolute; z-index: -1;"></p>').insertBefore($(this));
            }
            $(this).prev().html($(this).val());
        });
        var element = this[0];
        if (document.body.createTextRange) {
            var range = document.body.createTextRange();
            range.moveToElementText(element);
            range.select();
        } else if (window.getSelection) {
            var selection = window.getSelection();
            var range = document.createRange();
            range.selectNodeContents(element);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    };

    $(function () {
        $('.material-icons').click(function () {
            $(this).selectText();
            document.execCommand("copy");
        });
    });
</script>


<? foreach ($icons as $group_title => $group): ?>
	<div class="alert alert-info my-3"><h2 class="mb-0"><?= $group_title ?></h2></div>
	<? foreach ($group as $icon): ?>
		<span class="material-icons <?= $_REQUEST['TYPE'] ?> md-48" title="<?= $icon ?>"><?= $icon ?></span>
	<? endforeach; ?>
<? endforeach; ?>
