<?
/** @var \BlackFox\Engine $this */
$path = $this->TEMPLATE_PATH;
$lang = $this->GetLanguage();
?>
	<script>var lang = '<?= $lang ?>';</script>
<?
// Roboto
$this->AddHeaderStyle('https://fonts.googleapis.com/css?family=Roboto:300,400,500,700');

// jquery
?>
	<script src='<?= $path ?>/lib/jquery/jquery.min.js'></script>
	<script src='<?= $path ?>/lib/jquery-ui/jquery-ui.min.js'></script>
<?

// bootstrap
$this->AddHeaderStyle($path . '/lib/bootstrap/css/bootstrap.min.css');
$this->AddHeaderScript($path . '/lib/bootstrap/js/bootstrap.bundle.min.js');

// fontawesome
$this->AddHeaderStyle($path . '/lib/fontawesome/css/all.min.css');

// summernote
$this->AddHeaderStyle($path . '/lib/summernote/dist/summernote-bs4.css');
$this->AddHeaderScript($path . '/lib/summernote/dist/summernote-bs4.js');
if ($lang === 'ru') {
	$this->AddHeaderScript($path . '/lib/summernote/dist/lang/summernote-ru-RU.js');
}

// flatpickr
$this->AddHeaderScript($path . '/lib/flatpickr/flatpickr.min.js');
$this->AddHeaderStyle($path . '/lib/flatpickr/flatpickr.min.css');
if (!empty($lang) and $lang <> 'en') {
	$this->AddHeaderScript($path . '/lib/flatpickr/l10n/' . $lang . '.js');
}

// select2
$this->AddHeaderScript($path . '/lib/select2/js/select2.full.js');
if (!empty($lang) and $lang <> 'en') {
	$this->AddHeaderScript($path . '/lib/select2/js/i18n/' . $lang . '.js');
}
$this->AddHeaderStyle($path . '/lib/select2/css/select2.min.css');
$this->AddHeaderStyle($path . '/lib/select2-bootstrap/select2-bootstrap.min.css');

// custom
$this->AddHeaderScript($path . '/script.js');
$this->AddHeaderStyle($path . '/style.css');