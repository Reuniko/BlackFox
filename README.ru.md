[![en](https://img.shields.io/badge/lang-en-red.svg)](README.md)
[![ru](https://img.shields.io/badge/lang-ru-blue.svg)](README.ru.md)

# 🦊 BlackFox

BlackFox — PHP фреймворк, построенный на MVC-подходе. Даёт инструменты для разработки серьёзных,
нетиповых сайтов и приложений со сложной бизнес-логикой.

📖 **[Полная документация](https://reuniko.github.io/BlackFox/ru/index.html)**

## Возможности

- Независимые репозитории между фреймворком и вашим кодом
- Возможность переопределять файлы и классы фреймворка
- Автоматическая синхронизация структур таблиц
- Экстремально простой формат массива для фильтрации
- One-to-many, many-to-one, many-to-many связи
- Прямой доступ к SQL всегда доступен в одну строку, если ORM не хватает
- Административная страница для каждой таблицы базы данных
- Пользовательские типы данных
- Поддержка мультиязычности
- Наследование шаблонов

## Примеры

### Модель

```php
namespace Docs;
class Feedback extends \BlackFox\SCRUD {

	public $fields = [
		'ID'      => self::ID,
		'MOMENT'  => [
			'TYPE'     => 'DATETIME',
			'NAME'     => 'Момент',
			'NOT_NULL' => true,
		],
		'AUTHOR'  => [
			'TYPE'     => 'STRING',
			'NAME'     => 'Автор',
			'NOT_NULL' => true,
		],
		'MESSAGE' => [
			'TYPE' => 'TEXT',
			'NAME' => 'Сообщение',
		],
	];

	public function Create($element) {
		$element['MOMENT'] = time();
		return parent::Create($element);
	}
}
```

### Контроллер

```php
namespace Docs;
class UnitFeedback extends \BlackFox\Unit {

	public function Default() {
		$RESULT = Feedback::I()->Select([
			'SORT'  => ['ID' => 'DESC'],
			'LIMIT' => 10,
		]);
		return $RESULT;
	}

	public function AddFeedback(array $feedback) {
		$ID = Feedback::I()->Create($feedback);
		$this->Redirect("?ID={$ID}", 'Ваш отзыв был добавлен');
	}

}
```

`$RESULT`:

```php
[
	12 => [
		'ID'      => 12,
		'MOMENT'  => 1700000000,
		'AUTHOR'  => 'Анна',
		'MESSAGE' => 'Отлично!',
	],
	11 => [
		'ID'      => 11,
		'MOMENT'  => 1699999000,
		'AUTHOR'  => 'Максим',
		'MESSAGE' => 'Спасибо!',
	],
	// ...
]
```

### Админ-панель в одну строку

```php
\BlackFox\Adminer::Run(['SCRUD' => Docs\Feedback::I()]);
```

### Фильтрация — это просто массив

```php
$rooms = Rooms::I()->Select([
	'FIELDS' => ['ID', 'TITLE'],
	'FILTER' => [
		'~TITLE' => $_REQUEST['TITLE'],
	],
]);
```

`$rooms`:

```php
[
	5 => [
		'ID'    => 5,
		'TITLE' => 'Комната номер 567',
	],
	7 => [
		'ID'    => 7,
		'TITLE' => 'Комната номер 700',
	],
]
```

### Many-to-one

Поле типа `OUTER` — это ссылка на элемент другой таблицы:

```php
namespace BlackFox;
class Log extends SCRUD {
	public $fields = [
		'ID'   => self::ID,
		// ...
		'USER' => [
			'TYPE' => 'OUTER',
			'LINK' => 'Users',
			'NAME' => 'User',
		],
	];
}
```

Можно подтянуть поля связанного элемента и отфильтровать через связь, указав путь через точку:

```php
$logs = Log::I()->Select([
	'FIELDS' => [
		'ID',
		'USER' => ['ID', 'LOGIN', 'EMAIL'],
	],
	'FILTER' => [
		'USER.LOGIN' => 'Reuniko',
	],
]);
```

`$logs`:

```php
[
	42 => [
		'ID'   => 42,
		'USER' => [
			'ID'    => 1,
			'LOGIN' => 'Reuniko',
			'EMAIL' => 'reuniko@gmail.com',
		],
	],
	// ...
]
```

### One-to-many

Поле типа `INNER` — зеркальная сторона `OUTER`, виртуальное поле со списком элементов другой
таблицы, ссылающихся на текущую по обычному внешнему ключу. Здесь оно зеркалит связь `Log.USER`
из примера выше — один пользователь, много записей в логе:

```php
namespace BlackFox;
class Users extends SCRUD {
	public $fields = [
		'ID'   => self::ID,
		// ...
		'LOGS' => [
			'TYPE'      => 'INNER',
			'NAME'      => 'Logs',
			'LINK'      => 'BlackFox\Log',
			'INNER_KEY' => 'USER',
		],
	];
}
```

Выбирается точно так же, как и `OUTER`:

```php
$users = Users::I()->Select([
	'FIELDS' => ['ID', 'LOGIN', 'LOGS' => ['ID', 'MESSAGE']],
]);
```

`$users`:

```php
[
	1 => [
		'ID'    => 1,
		'LOGIN' => 'Reuniko',
		'LOGS'  => [
			90 => ['ID' => 90, 'MESSAGE' => 'Успешная авторизация'],
			77 => ['ID' => 77, 'MESSAGE' => 'Успешная авторизация'],
			// ...
		],
	],
	// ...
]
```

### Many-to-many

Комбинация `OUTER` и `INNER` через промежуточную таблицу:

```php
namespace BlackFox;
class Users2Groups extends SCRUD {
	public $fields = [
		'ID'    => self::ID,
		'USER'  => [
			'TYPE'    => 'OUTER',
			'LINK'    => 'Users',
			'FOREIGN' => 'CASCADE',
		],
		'GROUP' => [
			'TYPE'    => 'OUTER',
			'LINK'    => 'Groups',
			'FOREIGN' => 'CASCADE',
		],
	];
}
```

Открыть доступ к ней можно с любой стороны через `INNER`-поле, указывающее на промежуточную таблицу:

```php
namespace BlackFox;
class Users extends SCRUD {
	public $fields = [
		'ID'     => self::ID,
		// ...
		'GROUPS' => [
			'TYPE'      => 'INNER',
			'NAME'      => 'Groups',
			'LINK'      => 'BlackFox\Users2Groups',
			'INNER_KEY' => 'USER',
		],
	];
}
```

Выборка вернёт строки самой промежуточной таблицы; чтобы добраться до полей группы, нужно
углубиться ещё на уровень через её собственное поле `GROUP`:

```php
$users = Users::I()->Select([
	'FIELDS' => ['ID', 'LOGIN', 'GROUPS' => ['ID', 'GROUP' => ['ID', 'NAME']]],
]);
```

`$users`:

```php
[
	1 => [
		'ID'     => 1,
		'LOGIN'  => 'Reuniko',
		'GROUPS' => [
			1 => [
				'ID'    => 1,
				'GROUP' => [
					'ID'   => 1,
					'NAME' => 'Root',
				],
			],
			2 => [
				'ID'    => 2,
				'GROUP' => [
					'ID'   => 2,
					'NAME' => 'Редакторы',
				],
			],
		],
	],
	// ...
]
```

### Прямой доступ к базе данных

ORM никогда не закрывает доступ к живому соединению — оно всегда доступно в одну строку:

```php
$users = \BlackFox\Database::I()->Query("SELECT * FROM system_users");
```

`$users`:

```php
[
	[
		'ID'    => 1,
		'LOGIN' => 'Reuniko',
		'EMAIL' => 'reuniko@gmail.com',
	],
	// ...
]
```

### Автоматическая синхронизация структуры таблиц

При изменении описания полей BlackFox сравнивает их с реальной структурой базы данных и
синхронизирует её — без ручных миграций:

```php
(new \BlackFox\Scheme([
	Users::I(),
	Groups::I(),
	Users2Groups::I(),
]))->Synchronize();
```

## Установка

См. страницу [Установка](https://reuniko.github.io/BlackFox/ru/basics/install.html) в документации.

## Лицензия

[MIT](LICENSE)
