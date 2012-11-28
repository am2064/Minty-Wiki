What is this
============

Minty Wiki is a wiki that uses markdown files. The idea is quick and dirty deployment where you take the index.php file, and the markdown.php library, throw them into a web folder, then populate the folder with markdown files, and folders.

Overview
========

* Folders will turn into categories
* Markdown and Text files will be added as articles
* Configuration
	* $editting
		* Whether or not you wish to allow client-side editing of the files.
	* $backup
		* Whether or not files should be backed up when using client-side editing.
	* $wikiName
		* The name of the wiki which will appear in the top right corner by default
	* $css
		* The css to style the wiki with
	* $user_nav
		* User options for the top and bottom navigation bar for the wiki. It is pre-populated with a number of Markdown and Markdown Extra tutorials

Quick Installation
==================

1. Unzip or clone the repo
2. Copy, place, or link into your web root
3. Configure index.php
4. Create and remove markdown files as needed
5. Open index.php in web browser and test

##Make It Look Pretty

.6. Run 

```bash
/scripts/setup-bootstrap.sh
```

.7. Change 

```php5
$css
```	

to

```php5
$css=array("bootstrap/css/bootstrap.css","bootstrap/css/bootstrap-responsive.css","bootstrap/css/bootstrap-extra.css");
```

.8. Change

```php5
$exceptions
```

to

```php5
$exceptions=array("bootstrap","scripts");
```