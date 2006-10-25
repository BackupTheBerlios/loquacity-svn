<?php
/*
** Loquacity Weblog Software http://www.loquacity.info/
** Copyright (C) 2006 Kenneth Power <telcor@users.berlios.de>
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

/***************************************************************
*
*     Loquacity Configuration File for version: 0.8.0-alpha2
*
*/
/***************************************************************
*
*          Database Configuration
*
***************************************************************/
define('DB_USERNAME','l');
define('DB_PASSWORD','l');
define('DB_DATABASE','l2');
define('DB_HOST','localhost');
define('TBL_PREFIX','loq_');

/***************************************************************
*
*           Paths
*
***************************************************************/

//File system root directory where Loquacity application files reside
define('LOQ_APP_ROOT','/var/www/localhost/htdocs/l2/core/');

//
define('BLOGURL','http://localhost/l2/');
define('LOQ_APP_URL',BLOGURL.'core/');
//define('CLEANURLS',TRUE);
//define('URL_POST','__loq_url__item/%postid%/');
//define('URL_SECTION','__loq_url__section/%sectionname%/');
// ---- end of config ----
// leave this line alone
include_once(LOQ_APP_ROOT.'includes/init.php');

?>