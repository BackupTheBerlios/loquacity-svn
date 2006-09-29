=========
Contents:
=========

I Introduction

I.I About Loquacity
I.II Licence
I.III Credits
I.IV Bugs


II Instructions

II.I Prequisits
II.II The mySQL database and user.
II.III. Installing Loq.
II.IV Upgrade
II.V Change URLs
II.VI Problems


I. Introduction
===============

I About Loquacity
-------------
Loquacity is a simplified PHP blogging application, suited for non-technical and technical users alike. Its aim is to remain simple, yet powerful. Loquacity enables you to talk to the world in an easy way, using the method you prefer: text, audio, visual and more. Based on the bBlog blog engine, Loquacity is a furtherance of the project under new leadership and vision.

Here, you will find instructions and guidelines on how to install and use the package. For further advice, help yourself with the following resources:

* Documentation, including install instructions - <not completed>
* If you are stuck while reading the documentation, visit the Loquacity forums at  http://forum.loquacity.info. Please search through the forum first. Many other users might have gone through the same query you are about to look for or ask. If you still do not find the answer then, feel free to create a new thread and ask your question.


II Licence
----------
Loquacity is licenced under the General Public Licence v.2 - the GPL.
For a detailed licence, see licence.txt.


III Credits
-----------
Visit the "about" page in the admin panel for more information regarding the credits.


IV Bugs
-------
If you are unsure whether you have found a bug or not, feel free to enquire about it in the forum.

If you find a bug in Loquacity, you can report it in the bug tracker found at http://bugs.loquacity.info. For those who do not wish to create a profile in the bug tracker, you can use the following login: 
        username: bugs
        password: temp123
Note: This account is locked, only being able to report and view bugs.

If you find a security issue please refrain from filing it in the forum or bug tracker. Instead, report it directly to the project manager, Kenneth, at kenneth.power@gmail.com. Security and privacy is on the top of our priority list, and we would prefer to fix the issues as soon as possible without expanding the risks.




II. Instructions
================

I Prequisits
---------
Just before we start: If anything is unclear, detailed documentation about install can be obtained in the forum at http://loquacity.info/.............

Be sure to have all appropriate information and acces rights before you begin. You need:
- to be able to set file/folder permissions on the webserver,
- to be able to delete some files and folders, and
- to know the MySQL database, username and password (if not, see section II below).


II. The mySQL database and user.
---------

To create a database, you can either use a graphical installer such as mySQL-administrator (http://bla.com), phpmyadmin, or the Command Line (CLI) mySQL interface. For a quick reference, here is an example on how to create a mysql user 'loquser' and database 'loqdb' for Loquacity through the CLI interface.

** Login to mysql

localhost# mysql -u root -p
(enter your password)
mysql>

** Create a database, in our example, loqdb.

mysql> create database loqdb;
Query OK, 1 row affected (0.02 sec)

** Create a user, in our example, loquser, with password 'smellyfish', and assign to the database.
The syntax is 
grant all on db_name.* to user@host identified by 'password'; flush privileges;

So in our example, it would be as follows,

mysql> grant all on loqdb.* to loquser@localhost identified by 'smellyfish'; flush privileges;
Query OK, 0 rows affected (0.06 sec)
Query OK, 0 rows affected (0.03 sec)

That's it! Now all you need is to memorise your settings. In our example,
Database: loqdb
User: loquser
Password: smellyfish

Note: These are just examples. DO NOT use the database, username, or passwords mentioned above.


III. Installing Loq.
---------

Extract the "loquacity/" folder and its contents into your webserver, depending on where you want your blog to be. 
* If you want the blog to be the main content of your site, then extract the contents of "Loquacity/" directly into your webserver.  The end result would appear as http://yoursite.com/

* Alternatively, if you would like your blog to be in its own section in your website, then stick the Loquacity/ folder wherever need be. Rename the folder if you wish, eg. myblog/. The end result would appear as http://yoursite.com/myblog/



You now need to adjust file permissions. The following files or folders should be writable by the server (chmod 777 on linux/unix):
/generated/
/generated/cache/
/generated/cache/favorites.xml
/generated/templates/
config.php

The quickest way to do so in linux is to
chmod -R 777 generated/


When done, open your webbrowser and run the install script, which is called install.php.
http://mysite.com/install.php
or
http://mysite.com/myblog/install.php
depending on the steps mentioned above in subsection I.

You should now be greeted with a welcome message, and a choice to either perform a fresh installation, or an upgrade. If upgrading, skip to subsection IV - Upgrade. If installing from scratch, proceed onwards.

If all goes well, you will be presented with a few comfiguration settings to fill in. When done, click next, and if all goes well, you should be greeted with the final page.
 
* Delete install.php
* Delete the install folder
* Chmod the config.php so that it is not writable by the webserver

And finally, login to your new blog's admin panel with the username and password you specified during the install.
http://mysite.com/bblog/
or
http://mysite.com/myblog/bblog/
depending on the steps mentioned above in subsection I.

You may now write, delete and administer your blog. Have fun!


IV - Upgrade
----------

Just to be on the safe side, do a backup of your blog first. Backup your whole Loquacity files and folders, and backup the MySQL tables. 

@todo xushi: add an example on mysqldump

Now, delete your whole Loquacity installation files and folders, *EXCEPT* of your config.php (important!). A more recomended approach is to rename the whole folder into something else, resulting in a complete backup of your old blog version.

@todo xushi: example ...

Of course, if you customized your templates, or added/customized plugins, you should not delete those files, too. Otherwise will be lost (unless you did a backup, which you should! ;) 

Download the new release and unpack it onto your server as mentioned above in the installation section, replacing the empty config.php file with your personal one. 

Note: If you were using the blog's blogroll and uploaded the favorites.xml to your /cache/ folder, you now need to upload it again, because it got overwritten.

Be sure to re-set the appropriate file permissions again:
/generated/
/generated/cache/
/generated/cache/favorites.xml
/generated/templates/
config.php

The quickest way to do so in linux is to
chmod -R 777 config.php generated/

Finally, re-enter the installation menu through install.php, and choose the 'upgrade from' option in the option list. Follow through, and delete the install.php and install/ folders when done.

You may now log in again and go on using Loquacity.


V - Change URLs
---------------
NOTE: You only need to read this if you want your URLS to look like: 
www.example.com/blog/post/1/ instead of www.example.com/blog/index.php?postid=1

This looks better and is much better for search engines (Google, MSN, Yahoo...). Those search engines don't like pages, which are generated through a PHP script (which is the case in Loquacity), they like clean and clear, www-typical URLs and paths much better.

Loquacity supports those clean URLs, using a very simple method: htacces. Some servers support it, some don't - you will either need to try, or ask your admin/hosting company, if that works. Don't worry, you won't break anything, by simply trying it.

To enable those clean URLs, you need to rename the file "htaccess-cleanurls" (it's a AllowOverride setting in apache), which is located in the blog root, to ".htaccess" - don't forget the dot!. 

After you've done that, go to the /bblog/ directory and edit the config.php. There, at the bottom, you will find some lines speaking about clean URLs. You have to un-comment the lines, which contain PHP commands (not the ones with pure, informational text in them). A comment is indicated by two slashes ( // ) in PHP. If you remove those, the line(s) gets "active", uncommented again.

After you finished these two steps, your blog should now mainly use the clean URLs and the change is successfully done. But don't worry if some people set up links on your old ?postid=xx URLs - they won't stop working with this change, they still work. So every link outside there will still be usable :).

A little note for template designers: You may also need to edit the CSS / image links in the templates to make them absolute. 



VI - Problems
-----------

Here are some problems you may find,


 Fatal error: Smarty error: the $compile_dir '' does not exist, or is not a directory. in /var/www/localhost/htdocs/loquacity/bblog/3rdparty/smarty/libs/Smarty.class.php on line 1095

Cause: Your sessions might be disabled. To check, create a test.php file, and add the folowing line into it,, 
<?php phpinfo(); ?>
Save, and load it through the web browser. search for the following line,
session.save_path
Make sure it has a value (eg, /tmp).
If not, then you need to edit your php.ini file and uncomment the line.
When ready, restart your webserver, and try again.



Fatal error: Call to undefined function mysql_connect() in /var/www/localhost/htdocs/loquacity/bblog/3rdparty/adodb/drivers/adodb-mysql.inc.php on line 354

Cause: Your php can not communicate with mysql.
Note: If you're on Gentoo, use the mysql USE flag instead of the mysqli USE flag and recompile PHP.


Thank you for using Loquacity, and good luck :)

--
The Loquacity Team
http://loquacity.info
