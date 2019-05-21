# BlackFox
BlackFox is a PHP framework for web applications, simple outside, powerful inside.
Supporting, not limiting. It has been designed from programmer to programmers. 
It provides many things you need to help developing a serious non-typical website with complex model.

#### Status
The framework is ready to use.
The [documentation](docs/index.md) is wip.

## [Structure](docs/en/structure.md)
There are 2 (or more) folders inside your DOCUMENT_ROOT, each folder contain a **solution**.
Solution is a pack of modules, templates, root folder, etc.
BlackFox is a solution, your own folder is gonna be a solution too, each should be versioned separately.

## Engine
Class Engine does all dirty job about processing user request and generating an answer.
Features:
* Requests for non-static file go into Engine, so you need no call of header+footer in your root files
* It scans your modules and register all classes to lazy loader
* It generates page content first, then wraps it with template wrapper (if needed)
* It allows you to setup access to sections (folders)
* It catches many exceptions and shows corresponding pages, like 404 or login window
* It allows you to redefine any file from the framework by creating another one on the same path
* It allows you to override any class from the framework by inheriting it and setting redirect in config

## [SCRUD](docs/en/scrud.md): Search, Create, Read, Update, Delete. 
It allows you to create and use database tables in PHP OOP way.
Features:
* Define table structure as PHP array to be able to use it in code, to deploy it via VCS
* Lazy migration: compares the difference between your PHP code and real table structure and corrects it  
* Ability to override any method: 
add preliminary checks and on_success events right inside the corresponding method
* Ability to extend tables by inheritance
* Secure to eat raw user request
* Format of the filter conditions is designed the way you can program it withing html form
* Relations between tables: 1-to-many, many-to-1, any combinations (many-to-many is the combination)
* MySQL and Postgres support

It is very powerful with combination of Adminer: it builds administrative pages for tables.

## Unit
Unit is a class to create controller by inheriting it 
and also it is a folder with the same name, contains class and templates (views).
Features:
* Every public method is an action that can be called by user
* Every param in public method is a part of user request 
(param $page would be generated from $_REQUEST['page'] or $_FILES['page']),
if there are no default value for param - it becomes required
* You have options to exit the action: 
  * return a result as array
  * redirect
  * throw an exception, that will be handled and displayed
* Ability to change the sequence of actions by overriding method GetActions
* Options, allows you to launch same unit from various places with various params
* Inherit templates

## Adminer
This is a unit that eats SCRUD child as parameter and provides full control of the table:
* View section as table, filter, sort, pagination, personal settings of displayed filters and columns
* Forms for create and update elements
* Ability to delete elements

It can be extended in case you need additional functionality.

## Other classes
[AModule](modules/System/classes/abstract/AModule.php), 
[Database](modules/System/classes/database/Database.php), 
[User](modules/System/classes/User.php), 
[Exception](modules/System/classes/exceptions/Exception.php), 
[Cache](modules/System/classes/cache/Cache.php), 
[Utility](modules/System/classes/Utility.php).

## Other entities (childs of SCRUD)
[Files](modules/System/classes/entities/Files.php), 
[Users](modules/System/classes/entities/Users.php), 
[Groups](modules/System/classes/entities/Groups.php), 
[Users2Groups](modules/System/classes/entities/associative/Users2Groups.php), 
[Log](modules/System/classes/entities/Log.php), 
[Modules](modules/System/classes/entities/Modules.php), 
[Content](modules/System/classes/entities/Content.php), 
[Redirects](modules/System/classes/entities/Redirects.php).