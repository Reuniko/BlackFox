## Structure

You can redefine any file from the core by making your own core in any folder in server root. To register your own core add at the top of array of 'cores' in config.php a key-value pair:
* key - relative path to your core
* value - absolute path to your core

Usually there is only one folder, associated with current site, named **site**. This way you can easily manage version control of this folder. 

When framework is looking for file to include, it goes through all the cores from the top to the bottom. If the file has been founded, framework includes it and skips futher check.
