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
* Performs the actual installation
*/
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
            $this->loadconfiguration();
            $this->template = 'configuration.html';
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
        }
        else{
            $this->errors = $vc->errors;
        }
        return $rval; // xushi: comment this line and uncomment the one below it until fixed.
		//return true;
    }


    /**
    * Executes the install routines
    *
    */
    function install(){
        if($this->db()){
            define('TBL_PREFIX', $_SESSION['config']['table_prefix']);
            define('BLOGURL', $_SESSION['config']['blog_url']);
            $this->installplugins();
            $this->writeconfig();
        }
    }

	/**
	 * Connect to the database
     *
	 * Uses ADODB to handle this
	*/
    function db(){
        $dsn = 'mysql://'.$_POST['db_username'].':'.rawurlencode($_POST['db_password']).'@'.$_POST['db_host'].'/'.$_POST['db_database'];
        $db = NewADOConnection($dsn);
        if($db !== false){
            $this->db =& $db;
            $this->createdatabase();
        }
        return true;
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
        $ph = new pluginhandler($this->db);
        $ph->scan_for_plugins(LOQ_APP_ROOT.'plugins');
    }


	/**
	 * Store user-supplied configuration settings in config.php.
	 *
	*/
    function writeconfig(){
        if(file_exists(LOQ_APP_ROOT.'config.tmpl') && is_readable(LOQ_APP_ROOT.'config.tmpl')){
            $config = file_get_contents(LOQ_APP_ROOT.'config.tmpl');
            $config = str_replace('__pfx__', $_SESSION['config']['table_prefix'], $config);
            foreach($_SESSION['config'] as $setting=>$value){
                $config = str_replace('__'.$setting.'__', $value, $config);
            }
            $config = str_replace('__loq_version__', LOQ_CUR_VERSION, $config);
            $config = str_replace('__loq_app_root__', LOQ_APP_ROOT, $config);
            var_dump($config);
        }
        if(($fp = fopen(LOQ_APP_ROOT.'config.php', 'w+b')) !== false){
            if(fwrite($fp, "<?php\r$config\r?>") === false){
            }
            fclose($fp);
        }else{
        }
    }


    /**
    * Retrieves the proper SQL file and replaces the tokens with the user's configuration
    * Uses result to create the database and populate it with the configuration and sample data
    *
    * TODO abstract some of the specifics away so it is easier to support different DBs
    */
    function createdatabase(){
        /* For MySQL there are three main versions to worry about:
         * 4.0, 4.1 and 5.0
         * 4.1+ has real charset support, while < 4.1 doesn't
         */
        $info = $this->db->ServerInfo();
        $charset = null;
        if((strstr($info['version'], '4.1') !== false) || (strstr($info['version'], '5.0') !== false)){
            //We have a database that supports chartsets properly!
            $charset = ' CHARACTER SET utf8 COLLATE utf8_bin';
        }
        if(file_exists(LOQ_INSTALLER.'/sql/mysql.sql') && is_readable(LOQ_INSTALLER.'/sql/mysql.sql')){
            $sql = file_get_contents(LOQ_INSTALLER.'/sql/mysql.sql');
            $sql = str_replace('__pfx__', $_SESSION['config']['table_prefix'], $sql);
            
            if(!is_null($charset)){
                $sql = str_replace('__charset__', $charset, $sql);
            }
            foreach($_SESSION['config'] as $setting=>$value){
                $sql = str_replace('__'.$setting.'__', $value, $sql);
            }
            $sql = str_replace('__loq_version__', LOQ_CUR_VERSION, $sql);
            $statements = explode(';', $sql);
            foreach($statements as $line){
                if(trim($line) !== ''){
                    $this->db->Execute($line);
                }
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
