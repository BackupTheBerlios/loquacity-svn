<?php

/** 
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/ 
 * Copyright (c) 2006 Stanley Schade 
 * 
 * @package Loquacity 
 * @subpackage Installer 
 * @author Stan <lqc_stan@users.berlios.de> 
 * @copyright &copy; 2006 Stanley Schade 
 * @license    http://www.gnu.org/licenses/gpl.html GPL 
 * @link http://www.loquacity.info 
 * @since 0.8-alpha2 
 * 
 * LICENSE: 
 * 
 * Loquacity is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version. 
 * 
 * Loquacity is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details. 
 * 
 * You should have received a copy of the GNU General Public License 
 * along with Loquacity; if not, write to the Free Software 
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
 */

/**
* This file will make the changes necessary to migrate to
* -alpha1 from a bBlog-0.7.6 installation. It can be
* executed from the command line or your web browser.
* Many thanks to Stan for putting this together.
* A few code cleanups by Kenneth Power
*/

include('../config.php'); 
$tmpdb = NewADOConnection('mysql://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.'/'.DB_DATABASE.'?persist'); 
$sql = "INSERT INTO ".T_CONFIG." VALUES (null,'CAPTCHA_ENABLE','false'), (null,'CAPTCHA_WIDTH','200'),  (null,'CAPTCHA_HEIGHT','50'), (null,'CAPTCHA_CHARACTERS','5'),  (null,'CAPTCHA_LINES','70'),  (null,'CAPTCHA_ENABLE_SHADOWS','false'), (null,'CAPTCHA_OWNER_TEXT','false'), (null,'CAPTCHA_CHARACTER_SET','A-Z'), (null,'CAPTCHA_CASE_INSENSITIVE','false'), (null,'CAPTCHA_BACKGROUND',''), (null,'CAPTCHA_MIN_FONT','16'), (null,'CAPTCHA_MAX_FONT','25'), (null,'CAPTCHA_USE_COLOR','false'), (null,'CAPTCHA_GRAPHIC_TYPE','jpg')"; 
$tmpdb->Execute($sql);
?>
