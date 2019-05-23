# SCRUD
Is abbreviation for Search, Create, Read, Update, Delete.
It allows you to create and use database tables in PHP OOP way.

## Basics
To create a new table, create a children of SCRUD and define it's structure:
```
class Rooms extends \System\SCRUD {
    public $structure = [
        'ID'    => self::ID,
        'TITLE' => [
            'TYPE'     => 'STRING',
            'NAME'     => 'Room number',
            'NOT_NULL' => true,
        ],
    ];
}
```
It does all migration stuff by 
synchronizing table columns from your php code to database.
```
// put this code into Upgrade method of corresponding class of module
// and forget about migraion
Rooms::I()->Synchronize();
```
It allows you to Create:
```
$ID = Rooms::I()->Create(['TITLE' => 'Room number 567']);
```
It allows you to Update:
```
Rooms::I()->Update(5, ['TITLE' => 'Room number 5']);
Rooms::I()->Update([6, 7, 8], ['TITLE' => 'Storage']);
Rooms::I()->Update(['TITLE' => 'Unused'], ['TITLE' => 'Gum']);
```
It allows you to Delete:
```
Rooms::I()->Delete(5);
Rooms::I()->Delete([6, 7, 8]);
Rooms::I()->Delete(['TITLE' => 'Gum']);
```
It allows you to Read:
```
$room = Rooms::I()->Read(5);
// $room is array: ['ID' => 5, 'TITLE' => 'Room number 5']

$room = Rooms::I()->Read(5, ['TITLE']);
// $room is array: ['TITLE' => 'Room number 5']

$room = Rooms::I()->Read([], ['*'], ['ID' => 'DESC']);
// [] means empty condition, ['*'] means all fields, the third param is sort
// so the result is gonna be the top element by ID
```
It allows you to Search:
```
// each parameter is optional
$result = Rooms::I()->Search([
    'FIELDS' => ['ID', 'TITLE'],
    'FILTER' => [
        'ID' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        'TITLE' => 'Gum',
    ],
    'LIMIT' => 10,
    'PAGE' => 2,
]);
```
The result is gonna be an array with 2 keys: ELEMENTS and PAGER.
ELEMENTS is an array of rooms.
PAGER is an array, that can be used to build page navigation.
If you need no page navigation you can use method Select instead:
```
// each parameter is optional
$rooms = Rooms::I()->Select([
    'FIELDS' => ['ID', 'TITLE'],
    'FILTER' => [
        '%TITLE' => $_REQUEST['TITLE'],
    ],
]);
```
If you need only one column you can use method GetColumn:
```
$result = Rooms::I()->GetColumn([], 'TITLE');
// The result is gonna be associative array with keys - IDs, values -  column:
```

## Inheritance

You can override any method:
```
class Rooms extends \System\SCRUD {
    public $structure = [...];
    
    public function Create($fields) {
        // checks
        if ($fields['TITLE'] === 'swear words') {
            throw new Exception("No swear words allowed");
        }
        
        $ID = parent::Create($fields);
        
        // on successful create events
        \System\Log::I()->Create(['MESSAGE' => "The room #{$ID} has been created"]);
        
        return $ID;
    }
}
```
You can redefine any method:
```
class Log extends \System\SCRUD {
        
        ...

	public function Update($filter = [], $fields = []) {
		throw new ExceptionNotAllowed();
	}

	public function Delete($filter = []) {
		throw new ExceptionNotAllowed();
	}
}
```
You can extend the table by creating it's child and using Init method:
```
class RoomsWithWindows extends Rooms {

    public function Init() {
        $this->structure += [
            'WINDOWS_AMOUNT' => [
                'TYPE' => 'NUMBER',
                'NAME' => 'Windows amount',
            ],
        ];
    }
    
}
```
