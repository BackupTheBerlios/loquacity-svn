<?php
/**
 * install.php - bBlog installer
 * install.php - author: Eaden McKee <email@eadz.co.nz>
 *
 * bBlog Weblog http://www.bblog.com/
 * Copyright (C) 2003  Eaden McKee <email@eadz.co.nz>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * ratehr than duplicating effort, use as much internal stuff as possible
**/

if(file_exists(dirname(__FILE__).'/bblog')){
    define(LOQ_APP_ROOT, dirname(__FILE__).DIRECTORY_SEPARATOR.'bblog'.DIRECTORY_SEPARATOR);
    define(LOQ_INSTALLER, LOQ_APP_ROOT.'install');
    define('SMARTY_DIR', LOQ_APP_ROOT.'3rdparty/smarty/libs/');
    include_once(SMARTY_DIR.'Smarty.class.php');
    include_once(LOQ_APP_ROOT.'3rdparty/adodb/adodb.inc.php');
    include_once(LOQ_APP_ROOT.'includes/stringhandler.class.php');
    include_once(LOQ_APP_ROOT.'includes/confighandler.class.php');
    define(LOQ_CUR_VERSION, '0.8.0-alpha2');
}
else{
    die("Unsupported configuration. The installer does not support altered configurations, you must configure this application manually. If this is not your intent, perhaps your installation is corrupt. Try unzipping (de-compressing) all the files again.");
}
	// using sessions because it makes things easy
	session_start();
     //This should be guaranteed writable

	// start install all over, forget everything.
	if (isset($_GET['reset'])) {
		unset($config);
		unset($step);
		@session_destroy();
		header("Location: install.php");
		exit;
	}
    if(isset($_GET['install'])){
        /* can be prescan, install, postscan
        * If prescan works, we show the configure screen
        * The configure screen takes us to the install step
        * After the install the postscan occurs
        * An upgrade from bBlog skips only the display of the configure screen as we use the
        * existing config.php
        */
        //$db = new db($config['db_username'], $config['db_password'], $config['db_database'], $config['db_host']);
        include_once(LOQ_INSTALLER.'/installbase.class.php');
        include_once(LOQ_APP_ROOT.'includes/stringhandler.class.php');
        if(in_array($_GET['install'], array('prescan', 'install', 'postscan'))){
            include_once(LOQ_INSTALLER.'/install'.$_GET['install'].'.class.php');
            $class = 'install'.$_GET['install'];
            $step = new $class();
            $step->display();
        }
    }
    else{
        $smarty = new Smarty();
        $smarty->template_dir = LOQ_INSTALLER.'/templates';
        $smarty->compile_dir = ini_get("session.save_path");
        $smarty->assign('step', 0);
        $smarty->display('welcome.html');
    }
    //following is old upgrade code
	/*if(isset($config['upgrade_from'])) {
		if(file_exists('./install/upgrade.'.$config['upgrade_from'].'.php')) {
			include './install/upgrade.'.$config['upgrade_from'].'.php';
		} else {
			echo "<h3>Error</h3>";
			echo "<p>You have chosen an upgrade option, but the upgrade file (  install/upgrade.".$config['upgrade_from'].".php ) is missing";
			include 'install/footer.php';
			exit;
		}
	}
    if ((isset($config['install_type'])) && ($config['install_type'] == 'upgrade')) {
				echo "<h3>Upgrading</h3>";
				$intro_func = 'upgrade_from_'.$config['upgrade_from'].'_intro';
				if(function_exists($intro_func)) $intro_func();
			}
			$test = check_writable();
			if($test) echo "<p>Great, all working. <input type='submit' name='continue' value='Click here to continue' /></p>";
			else echo "<p>Please fix above errors, then <input type='submit' name='continue' value='Click here to try again' /></p>";
        $func = 'upgrade_from_'.$config['upgrade_from'].'_pre';
		if(function_exists($func)) {
			$func();
		} else {
			// this is really an error
			$step=5;
			echo "<p>Nothing to see here, <input type='submit' name='submit' value='Next &gt;' /></p>";
		}
		// upgrade.
		// if tables need to be created, such as MT or wordpress converstion, after this step go to step 4.
		// otherwise, in the case of a bBlog upgrade where tables _dont_ need to be created, go to step 5.
        $func = 'upgrade_from_'.$config['upgrade_from'].'_post';
		$func();
    */
