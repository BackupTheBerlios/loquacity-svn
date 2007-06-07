<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Installation
 * @author Kenneth Power <telcor@users.berlios.de>
 * @copyright Copyright &copy; 2006 Kenneth Power
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.loquacity.info
 * @since 0.8-alpha2
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
* Performs the actual installation
*/
include_once(LOQ_APP_ROOT.'includes/databasemanager.class.php');

class installinstall extends installbase{

	function installinstall(){
		installbase::installbase();
	}

	function __init(){
		$this->checkPrereq();
		if($this->checkConfigSettings()){
			$this->install();
		}
		if(isset($this->errors) && count($this->errors) > 0){
			$this->assign("errors", $this->errors);
			$this->setTemplate('configuration.html');
			$this->setAction('install.php?install=install');
		}
		else{
			$this->setTemplate('complete.html');
		}
    }


	/**
	* Verify all required settings exist
	*
	* @return bool
	*/
	function checkConfigSettings(){
		$rval = false;
		include_once(LOQ_APP_ROOT.'includes/validateconfiguration.class.php');
		$vc = new validateconfiguration();
		if($vc->isValid()){
		    $rval = true;
		}
		else{
		    $this->errors = $vc->errors;
		}
		return $rval;
	}


    /**
	* Executes the install routines
	*
	*/
	function install(){
		if($this->initDB()){
			$this->installplugins();
			$this->writeconfig();
		}
	}

	/**
	 * Initialize the database for new install
	 *
	 * Using the ADODB Library, attempts to create a DB connection. Returns TRUE
	 * on success and FALSE on failure. Error messages are logged manually. Due
	 * to the nature of this, PHP error handling is disabled in this function. If a successful connection
	 * is made, the database is then initialized.
	 *
	 * @return mixed
	*/
	function initDB(){
		$rval = false;
		ini_set("display_errors", "1");
		error_reporting(0);
		$dsn = 'mysql://'.$_POST['db_username'].':'.rawurlencode($_POST['db_password']).'@'.$_POST['db_host'].'/'.$_POST['db_database'];
		$db = NewADOConnection($dsn);
		if($db !== false){
			$this->db =& $db;
			if($this->createdatabase()){
				$rval = true;
			}
		}
		else{
			$this->errors[] = "Unable to connect to the database. Please check the database settings (username, password, etc) that you provided.";
		}
		#@ini_set("display_errors", "1"); #Only for development
		error_reporting(E_ALL); #Only for development
		return $rval;
	}


	/**
	 * Install the plugins
	 *
	 * Uses pluginhandler to perform this task
	 * 
	*/
	function installplugins(){
		include_once(LOQ_APP_ROOT.'includes/pluginhandler.class.php');
		define('T_PLUGINS',$_SESSION['config']['table_prefix'].'plugins');
		define('TBL_PREFIX', $_SESSION['config']['table_prefix']);
		define('BLOGURL', $_SESSION['config']['blog_url']);
		$ph = new pluginhandler($this->db);
		$ph->scan_for_plugins(LOQ_APP_ROOT.'plugins');
	}


	/**
	 * Store user-supplied configuration settings in config.php.
	 *
	*/
	function writeconfig(){
		if(file_exists(LOQ_INSTALLER.DIRECTORY_SEPARATOR.'config.tmpl') && is_readable(LOQ_INSTALLER.DIRECTORY_SEPARATOR.'config.tmpl')){
			$config = file_get_contents(LOQ_INSTALLER.DIRECTORY_SEPARATOR.'config.tmpl');
			$config = str_replace('__pfx__', $_SESSION['config']['table_prefix'], $config);
			foreach($_SESSION['config'] as $setting=>$value){
				//This is not the proper place to put this fix
				if($setting == 'fs_path'){
					if(strrpos($value, DIRECTORY_SEPARATOR) == strlen($value) && DIRECTORY_SEPARATOR == '\\'){
						$value = substr_replace($value, '\\', strlen($value));
					}
				}
				$config = str_replace('__'.$setting.'__', $value, $config);
			}
			$config = str_replace('__loq_version__', LOQ_CUR_VERSION, $config);
			$config = str_replace('__loq_app_root__', LOQ_APP_ROOT, $config);
		}
		if(($fp = fopen(LOQ_APP_ROOT.'config.php', 'w+b')) !== false){
			if(fwrite($fp, "<?php\n$config\n?>") === false){
			}
			fclose($fp);
		}else{
		}
	}

	/**
	 * Loads the database admin driver to create the database
	 *
	 */
	function createdatabase(){
		if(($driver = DatabaseManager::DatabaseManager($this->db, 'mysql')) !== FALSE){
			if($driver->createDB($_SESSION['config']) === false){
				$this->errors = array_merge($this->errors, $driver->errors);
				return false;
			}
			else{
				return true;
			}
		}
	}


	/**
	 * Pre-scan for any errors before continuing the installation
	 * This is an ugly hack that manually instantiates the InstallPrescan class
     * and executes the scan. This needs changed.
	*/
	function checkPrereq(){
		if(isset($_POST['prescan_errors'])){
			include_once(LOQ_INSTALLER.'/installprescan.class.php');
			$ps = new installprescan();
			if(isset($ps->errors) && count($ps->errors) > 0){
				$this->errors = $ps->errors;
			}
		}
	}
}
