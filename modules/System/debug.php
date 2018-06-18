<?php
if (!function_exists('debug')) {
	/**
	 * Базовая глобальная отладка
	 * работает только если в config.php определен ключ 'debug' => true
	 * не работает, если в реквесте указан ключ 'turn_off_debug' (удобно для финальных AJAX-запросов)
	 *
	 * @param mixed $data переменная для отладки
	 * @param string $title название переменной (не обязательно)
	 * @param string $mode способ отладки (не обязательно):
	 * - print_r - вывод print_r в невидимую textarea, отображается по нажатию клавиш alt + TILDE (по умолчанию)
	 * - var_export - вывод var_export в невидимую textarea, отображается по нажатию клавиш alt + TILDE
	 * - var_dump - вывод var_dump в невидимую textarea, отображается по нажатию клавиш alt + TILDE
	 * - console - выводится в консоль браузера
	 * - log - записывается в файл
	 * - email - отправляется на почту
	 * @param string $target путь отправки: имя файла или почтовый адрес
	 */
	function debug($data = [], $title = '', $mode = 'print_r', $target = '/debug.txt') {
		if (!\System\Engine::Instance()->config['debug']) {
			return;
		}
		if (isset($_REQUEST['turn_off_debug']) || isset($_REQUEST['TURN_OFF_DEBUG'])) {
			return;
		}
		if (in_array($mode, ['print_r', 'var_export', 'var_dump'])) {
			if ($mode === 'print_r') {
				$data = print_r($data, true);
			} elseif ($mode === 'var_export') {
				$data = var_export($data, true);
			} elseif ($mode === 'var_dump') {
				ob_start();
				var_dump($data);
				$data = ob_get_clean();
			}
			echo "<textarea class='debug' data-debug='{$title}' style='
				display: none; 
				resize: both; 
				position: relative; 
				z-index: 99999; 
				border: 1px green dashed;
				width: auto;
				'
			>{$title}=" . htmlspecialchars($data) . "</textarea>";
			static $need_js = true;
			if ($need_js) {
				$need_js = false;
				?>
				<script>
                    if (!window.engine_debug) {
                        document.addEventListener('keydown', function (event) {
                            // alt + TILDE
                            if (event.altKey && event.keyCode === 192) {
                                var debug = document.querySelectorAll('.debug');
                                debug.forEach(function (element) {
                                    element.style.display = (element.style.display == 'none') ? 'block' : 'none';
                                });
                            }
                        });
                        window.engine_debug = true;
                    }
				</script>
				<?
			}
		}
		if ($mode === 'console') {
			echo "<script>console.log('{$title}', " . json_encode($data, true) . ");</script>";
		}
		if ($mode === 'log') {
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . $target, "\r\n" . str_repeat('-', 50) . "\r\n" . $title . '=' . print_r($data, true), FILE_APPEND);
		}
		if ($mode === 'email') {
			mail($target, "debug from {$_SERVER['SERVER_NAME']}", $title . '=' . print_r($data, true));
		}
	}
}