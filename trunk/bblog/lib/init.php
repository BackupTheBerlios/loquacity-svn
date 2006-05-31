<?php
// init.php - Start the bBlog engine, include needed files
// init.php - author: Eaden McKee <email@eadz.co.nz>

/*
** bBlog Weblog http://www.bblog.com/
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

// prevent errors when _open_basedir is set
ini_set('include_path','./:../');

define('SMARTY_DIR', BBLOGROOT.'libs/smarty/libs/');

// include  needed files
include_once(SMARTY_DIR.'Smarty.class.php');
include_once(BBLOGROOT.'libs/adodb/adodb.inc.php');
include_once(BBLOGROOT.'libs/ez_sql.php');
include_once(BBLOGROOT.'inc/stringhandling.class.php');
include_once(BBLOGROOT.'inc/confighandler.class.php');
include_once(BBLOGROOT.'inc/posthandler.class.php');
include_once(BBLOGROOT.'inc/commenthandler.class.php');
include_once(BBLOGROOT.'inc/bBlog.class.php');
include_once(BBLOGROOT.'inc/functions.php');
include_once(BBLOGROOT.'inc/templates.php');


// start your engines
$bBlog = new bBlog();
$mtime = explode(" ",microtime());
$bBlog->begintime = $mtime[1] + $mtime[0];

// this is only here until I work out the best way to do theming.
//$bBlog->clear_compiled_tpl();


$bBlog->template_dir = BBLOGROOT.'templates/'.C_TEMPLATE;
$bBlog->compile_dir = BBLOGROOT.'compiled_templates/';

if(defined('IN_BBLOG_ADMIN')) {
       $bBlog->compile_id = 'admin';
} else 	{
	$bBlog->compile_id = C_TEMPLATE;
}

$bBlog->plugins_dir = array(BBLOGROOT.'bBlog_plugins', BBLOGROOT.'bBlog_plugins/smarty',BBLOGROOT.'libs/smarty/libs/plugins');
$bBlog->use_sub_dirs	= FALSE; // change to true if you have a lot of templates

define('BBLOG_VERSION',"0.8-alpha1");
$bBlog->assign("bBlog_version",BBLOG_VERSION);

?>