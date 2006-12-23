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
 * Handles upgrades from older versions of bBlog/Loquacity
 * 
 * TODO Break out some of this stuff into classes that can be shared between the upgrade and install processes
 */
 
class installupgrade extends installbase{
	function installupgrade(){
		$this->from = $_POST['upgrade_from'];
		installbase::installbase();
	}
	function __init(){
		if($this->upgradeConfig()){
			$this->upgradeDatabase();
		}
	}
	/**
	 * Updates the configuration file with newer options
	 * 
	 * @return bool
	 */
	function upgradeConfig(){
		$rval = false;
		switch($this->from){
			case 'bblog07':
				$rval = $this->__configFromBblog();
				break;
		}
		return $rval;
	}
	
	/**
	 * Converts the database to the current version
	 */
	function upgradeDatabase(){
		$rval = false;
        switch($this->from){
        	case 'bblog07':
        		$rval = $this->__dbFromBblog();
        		break;
        }
        return $rval;
	}
	/**
	 * Updates a bBlog 0.7.x configuration file to the current version
	 */
	function __configFromBblog(){
		$rval = false;
		if(($old_config = file_get_contents(LOQ_APP_ROOT.'config.php')) !== false){
			//Remove the standard include line from the config file to prevent errors
			$old_config = str_replace("include BBLOGROOT.'inc/init.php';", '', $old_config);
			if(($fp = fopen(LOQ_APP_ROOT.'config.php', 'w+b')) !== false){
	            if(fwrite($fp, "$old_config") !== false){
		            fclose($fp);
		            $dsn = 'mysql://'.DB_USERNAME.':'.rawurlencode(DB_PASSWORD).'@'.DB_HOST.'/'.DB_DATABASE;
			        $this->db = NewADOConnection($dsn);
					include_once(LOQ_APP_ROOT.'includes/BackupManager.class.php');
					$bm = new BackupManager($this->db);
					$bm->backup('upgrade');
					include_once(LOQ_APP_ROOT.'config.php');
					if(file_exists(LOQ_APP_ROOT.'config.tmpl') && is_readable(LOQ_APP_ROOT.'config.tmpl')){
			            $config = file_get_contents(LOQ_APP_ROOT.'config.tmpl');
			            $config = str_replace('__table_prefix__', TBL_PREFIX, $config);
			            $config = str_replace('__db_username__', DB_USERNAME, $config);
			            $config = str_replace('__db_password__', DB_PASSWORD, $config);
			            $config = str_replace('__db_database__', DB_DATABASE, $config);
			            $config = str_replace('__db_host__', DB_HOST, $config);
			            $config = str_replace('__blog_url__', BLOGURL, $config);
			            $config = str_replace('__loq_version__', LOQ_CUR_VERSION, $config);
			            $config = str_replace('__loq_app_root__', LOQ_APP_ROOT, $config);
			        }
			        if(($fp = fopen(LOQ_APP_ROOT.'config.php', 'w+b')) !== false){
			            if(fwrite($fp, "<?php\n$config\n?>") !== false){
			            	$rval = true; 
			            }
			            fclose($fp);
			        }
	            }
			}
			else{
				$this->errors[] = 'Unable to open the current configuration file for writing. Please verify '.LOQ_APP_ROOT.'config.php exists and ahs the necessary permissions assigned.';
			}
		}
		else{
			$this->errors[] = 'Unable to open the old configuration file for processing. Please verify that '.LOQ_APP_ROOT.'config.php is the correct file, exists and is readable and writable.';
		}
		return $rval;
	}
	/**
	 * Updates a bBLog 0.7.x database to the current version
	 * 
	 * @return bool
	 */
	function __dbFromBblog(){
		$rval = false;
		error_reporting(0);
        if($this->db !== false){
        	$info = $this->db->ServerInfo();
	        $charset = null;
	        $rval = false;
	        if((strstr($info['version'], '4.1') !== false) || (strstr($info['version'], '5.0') !== false)){
	        	$charset = ' utf8';
	        }
	        $sql_file = 'mysql_upgrade_bblog_alpha2.sql';
	        if(file_exists(LOQ_INSTALLER.'/sql/'.$sql_file) && is_readable(LOQ_INSTALLER.'/sql/'.$sql_file)){
	            $sql = file_get_contents(LOQ_INSTALLER.'/sql/'.$sql_file);
	            $sql = str_replace('__pfx__', TBL_PREFIX, $sql);
	            $sql = str_replace('__loq_version__', LOQ_CUR_VERSION, $sql);
		        if(!is_null($charset)){
                    $sql = str_replace('__charset__', $charset, $sql);
                }
                $statements = explode(';', $sql);
                foreach($statements as $line){
                    if(trim($line) !== ''){
                        $this->db->Execute($line);
                    }
                }
                $rval = true;
	        }
        }
        return $rval;
	}
}