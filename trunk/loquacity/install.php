<?php
/**
 * install.php - Loquacity installer
 * Copyright (C) 2006  Kenneth Power <kenneth.power@gmail.com>
 *
 * @package Loquacity
 * @subpackage Install
 * @author Eaden McKee <email@eadz.co.nz>
 * @copyright Copyright &copy; 2003  Eaden McKee <email@eadz.co.nz>
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
 

//For development, this should be set to E_ALL
// @TODO For normal usage, it should be set to 0
error_reporting(E_ALL);

//Build a suitable working environment to give us access to the application code
if(file_exists(dirname(__FILE__).'/core')){
    define('LOQ_APP_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR);
    define('LOQ_INSTALLER', LOQ_APP_ROOT.'install');
    define('SMARTY_DIR', LOQ_APP_ROOT.'3rdparty/smarty/libs/');
    include_once(SMARTY_DIR.'Smarty.class.php');
    include_once(LOQ_APP_ROOT.'3rdparty/adodb/adodb-errorhandler.inc.php');
    include_once(LOQ_APP_ROOT.'3rdparty/adodb/adodb.inc.php');
    include_once(LOQ_APP_ROOT.'includes/stringhandler.class.php');
    include_once(LOQ_APP_ROOT.'includes/confighandler.class.php');
    define('LOQ_CUR_VERSION', '0.8.0-alpha2');
}
else{
    die("Unsupported configuration. The installer does not support altered configurations, you must configure this application manually. If this is not your intent, perhaps your installation is corrupt. Try unzipping (de-compressing) all the files again.");
}

/*
* The install variable instructs the installer our current progress through installation
*/
if(isset($_GET['install'])){
    /* install can have the following values: prescan, install, postscan
    * If prescan works, we show the configure screen
    * The configure screen takes us to the install step
    * After the install the postscan occurs
    * An upgrade from bBlog skips only the display of the configure screen as we use the
    * existing config.php
    */
    startSession();
    include_once(LOQ_INSTALLER.'/installbase.class.php');
    include_once(LOQ_APP_ROOT.'includes/stringhandler.class.php');
    include_once(LOQ_APP_ROOT.'3rdparty/adodb/adodb-errorhandler.inc.php');
    include_once(LOQ_APP_ROOT.'3rdparty/adodb/adodb.inc.php');
    /*
    Check for and create an Install object of our current progress
    */
    if(isset($_GET['install'])){
    	if($_GET['install'] === 'reset'){
	        @session_destroy();
	        header("Location: install.php");
	        exit;
	    }
	    else if(in_array($_GET['install'], array('prescan', 'install', 'postscan'))){
	        include_once(LOQ_INSTALLER.'/install'.$_GET['install'].'.class.php');
	        $class = 'install'.$_GET['install'];
	        $step = new $class();
	        $step->display();
	    }
    }
}
else{
    /*
    * This is the initial entry point for the installer
    *
    * Although it looks odd, the following code resets the session
    * guaranteeing a fresh start upon each visit
    */
    startSession();
    $_SESSION = array();
    if (isset($_COOKIE[session_name('LoquacityInstaller')])) {
       setcookie(session_name('LoquacityInstaller'), '', time()-42000, '/');
    }
    session_destroy();
    startSession();
    $smarty = new Smarty();
    $smarty->template_dir = LOQ_INSTALLER.'/templates';
    $smarty->compile_dir = ini_get("session.save_path");
    $smarty->assign('step', 0);
    $smarty->display('welcome.html');
}
    
function startSession(){
    session_name('LoquacityInstaller');
    session_start();
}
