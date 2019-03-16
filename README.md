# Black Fox
Framework for web applications.

## Installation
* Setup server with PHP 7+ and MySQL 5.5+ environment
* Download and place files of framework into any folder inside your server root (for example: **core**)
* Copy all files from /**core**/config/ into your server root
* Update /config.php with requisites of the database
* Launch file /**core**/install.php from the php console
* If your HTTP server is different from Apache\Nginx, configure it in such a way that any user request for non-static file (\*.php and any folder) will be redirected to /core/engine.php

There we go! Now you can log into admin panel (/admin/) with default requisites: Root@123456.

## File management

You can redefine any file from the core by making your own core in any folder in server root. To register your own core add at the top of array of 'cores' in config.php a key-value pair:
* key - relative path to your core
* value - absolute path to your core

Usually there is only one folder, associated with current site, named **site**. This way you can easily manage version control of this folder. 

When framework is looking for file to include, it goes through all the cores from the top to the bottom. If the file has been founded, framework includes it and skips futher check.