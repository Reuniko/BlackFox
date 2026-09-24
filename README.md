[![en](https://img.shields.io/badge/lang-en-red.svg)](README.md)
[![ru](https://img.shields.io/badge/lang-ru-blue.svg)](README.ru.md)

# 🦊 BlackFox

BlackFox is a PHP framework built on the MVC approach. It provides tools to help you develop a
serious, non-typical web-site or application with an extremely complex business model.

📖 **[Full documentation](https://reuniko.github.io/BlackFox/en/index.html)**

## Features

- Independent VCS between the framework and your code
- Ability to override framework files and classes
- Automatic synchronization of table structures
- Extremely simple array format for filtering
- One-to-many, many-to-one, many-to-many links
- Direct SQL access is still one call away, whenever the ORM isn't enough
- Administrative page for each database table
- Custom data types
- Multi-language support
- View inheritance

## Examples

### Model

```php
namespace Docs;
class Feedback extends \BlackFox\SCRUD {

	public $fields = [
		'ID'      => self::ID,
		'MOMENT'  => [
			'TYPE'     => 'DATETIME',
			'NAME'     => 'Moment',
			'NOT_NULL' => true,
		],
		'AUTHOR'  => [
			'TYPE'     => 'STRING',
			'NAME'     => 'Author',
			'NOT_NULL' => true,
		],
		'MESSAGE' => [
			'TYPE' => 'TEXT',
			'NAME' => 'Message',
		],
	];

	public function Create($element) {
		$element['MOMENT'] = time();
		return parent::Create($element);
	}
}
```

### Controller

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
		$this->Redirect("?ID={$ID}", 'Your feedback has been added');
	}

}
```

`$RESULT`:

```php
[
	12 => [
		'ID'      => 12,
		'MOMENT'  => 1700000000,
		'AUTHOR'  => 'Anna',
		'MESSAGE' => 'Great!',
	],
	11 => [
		'ID'      => 11,
		'MOMENT'  => 1699999000,
		'AUTHOR'  => 'Max',
		'MESSAGE' => 'Thanks!',
	],
	// ...
]
```

### Admin panel, in one line

```php
\BlackFox\Adminer::Run(['SCRUD' => Docs\Feedback::I()]);
```

### Filtering is just an array

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
		'TITLE' => 'Room number 567',
	],
	7 => [
		'ID'    => 7,
		'TITLE' => 'Room number 700',
	],
]
```

### Many-to-one

An `OUTER` field is a reference to an element of another table:

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

Fetch fields of the linked element, and filter through the link by dotting into it:

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

An `INNER` field is the mirror side of `OUTER` — a virtual field listing elements of another
table that reference the current one, matched by a plain foreign key. Here it mirrors the
`Log.USER` link from the example above — one user, many log entries:

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

It's selected just like `OUTER`:

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
			90 => ['ID' => 90, 'MESSAGE' => 'Successful authorization'],
			77 => ['ID' => 77, 'MESSAGE' => 'Successful authorization'],
			// ...
		],
	],
	// ...
]
```

### Many-to-many

Combine `OUTER` and `INNER` through a staging table:

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

Expose it from either side with an `INNER` field pointing at the staging table:

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

Selecting it returns the staging rows; nest one level further, through the staging row's own
`GROUP` field, to reach the group's fields:

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
					'NAME' => 'Editors',
				],
			],
		],
	],
	// ...
]
```

### Direct database access

The ORM never locks you in — the underlying connection is always one call away:

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

### Automatic table structure sync

Whenever your field definitions change, BlackFox compares them against the real database
structure and synchronizes it — no hand-written migrations:

```php
(new \BlackFox\Scheme([
	Users::I(),
	Groups::I(),
	Users2Groups::I(),
]))->Synchronize();
```

## Install

See the [Install](https://reuniko.github.io/BlackFox/en/basics/install.html) page in the documentation.

## License

[MIT](LICENSE)
