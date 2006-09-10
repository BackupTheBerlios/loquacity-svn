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
 * Start the bBlog engine, include needed files
 *
 * @version $Revision$
 */
if ( ! is_dir(BBLOGROOT) ) {
  // throw meaningful error here ( OK tim ! )
  echo "There was an error : BBLOGROOT is not a directory. Please check that you have configured bBlog correctly by checking values in config.php";
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
    ini_set('include_path', BBLOGROOT.PATH_SEPARATOR.$cur_path);
}

define('SMARTY_DIR', BBLOGROOT.'3rdparty/smarty/libs/');

// include  needed files
include_once(SMARTY_DIR.'Smarty.class.php');
include_once(BBLOGROOT.'3rdparty/adodb/adodb.inc.php');
include_once(BBLOGROOT.'includes/stringhandler.class.php');
include_once(BBLOGROOT.'includes/confighandler.class.php');
include_once(BBLOGROOT.'includes/posthandler.class.php');
include_once(BBLOGROOT.'includes/commenthandler.class.php');
include_once(BBLOGROOT.'includes/sectionhandler.class.php');
include_once(BBLOGROOT.'includes/bBlog.class.php');
include_once(BBLOGROOT.'includes/templates.php');


// start your engines
$bBlog = new bBlog();
/* $mtime = explode(" ",microtime());
$bBlog->begintime = $mtime[1] + $mtime[0]; */

// this is only here until I work out the best way to do theming.
//$bBlog->clear_compiled_tpl();


/* $bBlog->template_dir = BBLOGROOT.'templates/'.C_TEMPLATE;
$bBlog->compile_dir = BBLOGROOT.'generated/templates/'; */

if(defined('IN_BBLOG_ADMIN')) {
       $bBlog->compile_id = 'admin';
} else 	{
	$bBlog->compile_id = C_TEMPLATE;
}

/* $bBlog->plugins_dir = array(BBLOGROOT.'plugins', BBLOGROOT.'plugins/smarty',BBLOGROOT.'3rdparty/smarty/libs/plugins');
$bBlog->use_sub_dirs	= FALSE; // change to true if you have a lot of templates
 */
define('BBLOG_VERSION',"0.8-alpha2");
$bBlog->assign("bBlog_version",BBLOG_VERSION);

?>