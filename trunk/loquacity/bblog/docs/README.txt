=========
Contents:
=========

I Introduction

I.I About Loquacity
I.II Licence
I.III Credits
I.IV Bugs


II Instructions

II.I Install
II.II Upgrade
II.III Change URLs


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

I Install
---------
Just before we start: If anything is unclear, detailed documentation about install can be obtained here: www.bblog.com/docs/Installing-bBlog

You will need to do certain things during or before install, so be sure to have all appropriate information and acces rights. You need:
- to be able to set file/folder permissions on the webserver,
- to be able to delete some files and folders,
- to know the MySQL database, username and password, and
- to know the folder schemes for your blog.

After you downloaded and uploaded the bBlog archive to your server, extract the folder "blog", including all files and subfolders, to the desired folder on your webserver. The folder "blog" will be the root directory of your blog - the place, where you later will read it with your webbrowser. 

Before installing bBlog, you need to adjust some of the filme permissions. These files or folders should be writable by the server (chmod 777 on linux):
/cache/
/cache/favorites.xml
/compiled_templates/
/config.php

So, everything is extracted and in place now. Open your webbrowser and run the install script, which is called install.php and can be found in the /bblog/ folder.
The installer will guide you through the install process and tell you what to do.

After you finished the whole install process, you can go to www.yoursever.com/bblog/ and log in, using the username and password you specified during install. If, of course, you installed your blog in some subdirectories, you will have to make the URL fit with those subdirectories :).

You may now write, delete and administrate your blog. Have fun!


II Upgrade
----------
Upgrading from version 0.7.2 up to 0.7.6 is easy. Although, before doing anything, we'd recommend reading through the whole guide completely and then do the upgrade. So you know, what's going to happen :).

Just to be on the safe side, do a backup of your blog first. Backup your whole bBlog files and folders, and backup the MySQL tables. 

Now, delete your whole bBlog installation files and folders, *EXCEPT* of your config.php (important!). 

Of course, if you customized your templates, or added/customized plugins, you should not delete those files, too. Otherwise they're lost (unless you did a backup, which you should! ;) 

Okay, download the new release from www.bblog.com. Now you should either remove the config.php from the ARCHIVE (!), you just downloaded and unpack it on your server, or you unpack it on your local harddrive, remove the config.php there and then upload the whole thing. The important thing is, that your original config.php on your webserver remains untouched!

If you were using the blog's blogroll and uploaded the favorites.xml to your /cache/ folder, you now need to upload it again, because it got overwritten.

Be sure to set the appropriate file permissions again. They need to be writable by the server:
/cache/
/cache/favorites.xml
/compiled_templates/

on Linux this would be: chmod -R 777 cache compiled_templates

After all this is done, allyou have to do is start the installer again, and go through the "Upgrade from bBlog 0.7.4" section. It will update the database.

After the script is finished, you have succesfully upgraded your bBlog. Using the log in area at /bblog/, you may now log in again and go on using bBlog.


III Change URLs
---------------
NOTE: You only need to read this if you want your URLS to look like: 
www.example.com/blog/post/1/ instead of www.example.com/blog/index.php?postid=1

This looks better and is much better for search engines (Google, MSN, Yahoo...). Those search engines don't like pages, which are generated through a PHP script (which is the case in bblog), they like clean and clear, www-typical URLs and paths much better.

bBlog supports those clean URLs, using a very simple method: htacces. Some servers support it, some don't - you will either need to try, or ask your admin/hosting company, if that works. Don't worry, you won't break anything, by simply trying it.

To enable those clean URLs, you need to rename the file "htaccess-cleanurls" (it's a AllowOverride setting in apache), which is located in the blog root, to ".htaccess" - don't forget the dot!. 

After you've done that, go to the /bblog/ directory and edit the config.php. There, at the bottom, you will find some lines speaking about clean URLs. You have to un-comment the lines, which contain PHP commands (not the ones with pure, informational text in them). A comment is indicated by two slashes ( // ) in PHP. If you remove those, the line(s) gets "active", uncommented again.

After you finished these two steps, your blog should now mainly use the clean URLs and the change is successfully done. But don't worry if some people set up links on your old ?postid=xx URLs - they won't stop working with this change, they still work. So every link outside there will still be usable :).

A little note for template designers: You may also need to edit the CSS / image links in the templates to make them absolute. 

Thank you for using bBlog, and good luck :)

--
The Loquacity Team
http://loquacity.info