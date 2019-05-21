## Structure

There are 2 (or more) folders inside your DOCUMENT_ROOT, each folder contain a **solution**.
Solution is a pack of modules, templates, root folder, etc.
BlackFox is a solution, your own folder is gonna be a solution too, each should be versioned separately.

You can redefine any file from the core by making your own core in any folder in server root. To register your own core add at the top of array of 'cores' in config.php a key-value pair:
* key - relative path to your core
* value - absolute path to your core

Usually there is only one folder, associated with current site, named **site**. This way you can easily manage version control of this folder. 

When framework is looking for file to include, it goes through all the cores from the top to the bottom. If the file has been founded, framework includes it and skips futher check.
