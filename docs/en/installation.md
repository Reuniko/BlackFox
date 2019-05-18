# Installation

* Setup server with PHP 7+ and MySQL 5.5+ environment
* Download and place files of framework into any folder inside your server root (for example: **core**)
* Copy all files from /**core**/config/ into your server root
* Update /config.php with requisites of the database
* Launch file /**core**/install.php from the php console
* If your HTTP server is different from Apache\Nginx, configure it in such a way that any user request for non-static file (\*.php and any folder) will be redirected to /core/engine.php

There we go! Now you can log into admin panel (/admin/) with default requisites: Root@123456.
