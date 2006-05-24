<?php
/*

   '||     '||'''|,  '||`
    ||      ||   ||   ||
    ||''|,  ||;;;;    ||  .|''|, .|''|,
    ||  ||  ||   ||   ||  ||  || ||  ||
   .||..|' .||...|'  .||. `|..|' `|..||
                                     ||
          .7                      `..|'

** bBlog Weblog Software http://www.bblog.com/
** Copyright (C) 2003  Eaden McKee <email@eadz.co.nz>
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version. 
**
** This program is distributed in the hope that it will be useful, 
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* login details are stored in the database */



/* MySQL details */

// MySQL database username
define('DB_USERNAME','root');

// MySQL database password
define('DB_PASSWORD','');

// MySQL database name
define('DB_DATABASE','bblogRevamped');

// MySQL hostname
define('DB_HOST','localhost');

// prefix for table names if you're installing
// more than one copy of bblog on the same database
// don't change this unless you know what you're doing.
define('TBL_PREFIX','bB_');

/* file and paths */

// Full path of the directory where you've installed bBlog
// ( i.e. the bblog folder )
define('BBLOGROOT','/home/kpower/public_html/blog/bblog/');

/* URL config */

// URL to your blog ( one folder below the 'bBlog' folder )
// e.g, if your bBlog folder is at www.example.com/blog/bblog, your
// blog will be at www.example.com/blog/
define('BLOGURL','http://localhost/~kpower/blog/');

// URL to the bblog folder via the web.
// Becasue if you're using clean urls and news.php as your BLOGURL,
// we can't automatically append bblog to it.
define('BBLOGURL',BLOGURL.'bblog/');

// Clean or messy urls ? ( READ README-URLS.txt ! )
define('CLEANURLS',TRUE);
define('URL_POST','http://localhost/~kpower/blog/item/%postid%/');
define('URL_SECTION','http://localhost/~kpower/blog/section/%sectionname%/');

// ---- end of config ----
// leave this line alone
include BBLOGROOT.'inc/init.php';
?>