# vbsNews!

> Please, for the love of everything that is holy, don't use this code.

**vbsNews** is a news management (ie: blogging) "platform" (ie: scripts) that I wrote starting in 2003
short after I started learning PHP. This surviving backup (that I recently found on a CD) is from 2004. 
I finally gave up on the project in 2006. It was used by literally dozens of people in its heyday.

The following is the surviving readme.

***

This file contains important information about the installation and use of vbsNews. Please read this file 
carefully.

## What is vbsNews?
vbsNews is a news management system using the power of PHP and MySQL. Through an easy to use, yet powerful User 
Control Panel, you can add/remove and modify news items, add/remove users, and setup advanced settings that 
control the way your news looks.

##Requirements
vbsNews requires PHP 4.3+ and MySQL 3.23+. Although vbsNews wasn't tested on anything lower than these 
requirements, it may function properly. If you attempt to use it on lower versions, please contact us and let 
us know whether or not it works.

## Installation
 1. Before you can use vbsNews, you must have a MySQL database ready to use. vbsNews does not require its own database, 
so it can be used within an existing one. If you do not have a database setup and do not know how, contract your 
system administrator for help. Most hosting companies have a User Control panel that allows you to adminster your 
MySQL databases. Make sure you have one ready to use before you begin.
 2. Open the file `dbconfig.php` and modify the first five lines as described in the file. If you don't know the proper 
values, please contact your system administrator for help.
 3. Open the file `display.php` and modify the first line as described in the file. If you don't know the proper values, 
please contact your system administrator for help.
 4. Upload all files (except for `readme.html` and `versions.html` files) to a directory of your choice. It is recommeded 
that you create a new directory (such as `http://yoursite.com/news/`) for the files.
 5. Run the `install.php` file and follow the instructions given. When you are done with this, vbsNews is ready for use.
 6. **Important:** It is recommended that you delete the `install.php` and `db_install.php` files after installation for 
security reasons.

## Updating
 1. Unzip the vbsNews file and replace all files EXCEPT `install.php` and `dbconfig.php` (if it exists). If the file doesn't 
exist in your installation, unzip it as well.
 2. If you had to unzip the `dbconfig.php` file, open it up and modify the first few lines as described in the file. When 
finished, open up `display.php` and replace the first line as described.
 3. Upload the new files (if you are using vbsNews on a remote server, replacing the already existing files.
 4. Run `update.php`. This file will update your MySQL database with the changes made in the new release. When finished, delete this file (and `install.php` if it exists on your server).
 5. Your new version of vbsNews should now be ready to use.

## General Use
Below is general code that will include the news within your page. You MUST put this code within a .php page. If your 
pages are currently HTML pages, it is as simple as changing the extension to .php. The HTML file will still function 
properly. 

To include news, paste this snippet where you want the news to appear:

```php
<?php
    include_once '/path/to/news/folder/display.php';
    execute();
?>
```

It's that simple. Make sure `/path/to/news/folder/` is the correct path to where you installed vbsNews.

For headers (where 5 is the number you want to show):

```php
<?php
    include_once '/path/to/news/folder/display.php';
    headers(5);
?>
```

For a detailed explaination on how to include the news within your pages, check out the User Manual. 

## Copyright
This software and the name vbsNews is Copyright 2003 VBShelf. All rights reserved.
