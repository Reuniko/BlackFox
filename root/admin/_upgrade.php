<?php
/* @var \System\Engine $this */
\System\Module::Instance()->Upgrade();
\Admin\Module::Instance()->Upgrade();
?>
<div class="alert alert-info">Апгрейд завершен</div>