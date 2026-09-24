// Структура сайдбара для русской версии доков.
// Пути указаны относительно папки этого файла (/ru/), например "classes/scrud.html".
// Рендерится /assets/sidebar-render.js - там же логика подсветки текущей страницы.
var BLACKFOX_SIDEBAR = [
	{
		"name": "Основы",
		"children": [
			{"name": "Установка", "href": "basics/install.html"},
			{"name": "Конфигурирование", "href": "basics/config.html"},
			{"name": "Структура", "href": "basics/structure.html"},
			{"name": "Виртуальный корень", "href": "basics/root.html"},
			{"name": "Шаблон и обертка", "href": "basics/template.html"},
			{"name": "Глобальные функции", "href": "basics/functions.html"}
		]
	},
	{
		"name": "Классы",
		"children": [
			{"name": "Exception", "href": "classes/exception.html"},
			{"name": "Instance", "href": "classes/instance.html"},
			{"name": "Engine", "href": "classes/engine.html"},
			{"name": "Database", "href": "classes/database.html"},
			{"name": "SCRUD", "href": "classes/scrud.html"},
			{"name": "Scheme", "href": "classes/scheme.html"},
			{"name": "Unit", "href": "classes/unit.html"},
			{"name": "Cache", "href": "classes/cache.html"},
			{"name": "User", "href": "classes/user.html"},
			{"name": "Users", "href": "classes/users.html"},
			{"name": "Redirects", "href": "classes/redirects.html"},
			{"name": "Pages", "href": "classes/pages.html"},
			{"name": "Adminer", "href": "classes/adminer.html"}
		]
	}
];
