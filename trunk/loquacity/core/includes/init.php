<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Administration
 * @author Eaden McKee <email@eadz.co.nz>
 * @copyright &copy; 2003  Eaden McKee <email@eadz.co.nz>
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.loquacity.info
 * @since 0.8-alpha1
 *
 * LICENSE:
 *
 * This file is part of Loquacity.
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
 * Build the necessary environment needed for Loquacity to function.
 * Remove all magic_quote enabled escapes - this is to ensure we have the same environment everywhere
 */
if ( ! is_dir(LOQ_APP_ROOT) ) {
  // throw meaningful error here
  echo "There was an error : LOQ_APP_ROOT is not a directory. Please check that you have configured Loquacity correctly by checking the values in config.php";
  die();
   
}

// define the table names
define('T_CONFIG',TBL_PREFIX.'config');
define('T_POSTS',TBL_PREFIX.'posts');
define('T_SECTIONS',TBL_PREFIX.'sections');
define('T_MODIFIERS',TBL_PREFIX.'modifiers');
define('T_PLUGINS',TBL_PREFIX.'plugins');
define('T_COMMENTS',TBL_PREFIX.'comments');
define('T_AUTHORS',TBL_PREFIX.'authors');
define('T_LINKS',TBL_PREFIX.'links');
define('T_CATEGORIES',TBL_PREFIX.'categories');
define('T_RSS',TBL_PREFIX.'rss');

// If ini_set is available, set our include path
if(function_exists('ini_set')){
    $cur_path = ini_get('include_path');
    ini_set('include_path', LOQ_APP_ROOT.PATH_SEPARATOR.$cur_path);
}

define('SMARTY_DIR', LOQ_APP_ROOT.'3rdparty/smarty/libs/');

// include  needed files
include_once(SMARTY_DIR.'Smarty.class.php');
include_once(LOQ_APP_ROOT.'3rdparty/adodb/adodb.inc.php');
include_once(LOQ_APP_ROOT.'includes/stringhandler.class.php');
include_once(LOQ_APP_ROOT.'includes/confighandler.class.php');
include_once(LOQ_APP_ROOT.'includes/posthandler.class.php');
include_once(LOQ_APP_ROOT.'includes/commenthandler.class.php');
include_once(LOQ_APP_ROOT.'includes/sectionhandler.class.php');
include_once(LOQ_APP_ROOT.'includes/Loquacity.class.php');
include_once(LOQ_APP_ROOT.'includes/templates.php');

//Remove magic quotes
foreach($_POST as $key=>$val){
	$_POST[$key] = stringHandler::removeMagicQuotes($val);
}
foreach($_GET as $key=>$val){
	$_GET[$key] = stringHandler::removeMagicQuotes($val);
}
unset($key);
unset($val);

$loq = new Loquacity();
if(defined(C_CAPTCHA_ENABLE) && C_CAPTCHA_ENABLE == 'true'){
    include_once(LOQ_APP_ROOT.'3rdparty/captcha/php-captcha.inc.php');
}
/* $mtime = explode(" ",microtime());
$loq->begintime = $mtime[1] + $mtime[0]; */


/* $loq->template_dir = LOQ_APP_ROOT.'templates/'.C_TEMPLATE;
$loq->compile_dir = LOQ_APP_ROOT.'generated/templates/'; */

if(defined('IN_BBLOG_ADMIN')) {
	$loq->compile_id = 'admin';
	$loq->template_dir = LOQ_APP_ROOT.'includes/admin_templates';
} else 	{
	$loq->compile_id = C_TEMPLATE;
}

/* $loq->plugins_dir = array(LOQ_APP_ROOT.'plugins', LOQ_APP_ROOT.'plugins/smarty',LOQ_APP_ROOT.'3rdparty/smarty/libs/plugins');
$loq->use_sub_dirs	= FALSE; // change to true if you have a lot of templates
 */
define('BBLOG_VERSION',"0.8-alpha2");
$loq->assign("bBlog_version",BBLOG_VERSION);

?>