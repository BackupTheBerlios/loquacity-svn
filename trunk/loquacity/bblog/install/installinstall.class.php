<?php

class installinstall extends installbase{
	function installinstall(){
        installbase::installbase();
        $this->template = 'complete.html';
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
        //only table prefix is not required
        $rval = true;
        foreach(array('blog_name', 'blog_description', 'author_name', 'login_name', 'login_password', 'verify_password', 'email_address', 'db_username', 'db_password', 'db_database', 'db_host', 'blog_url', 'fs_path') as $setting){
            if(isset($_POST[$setting]) && strlen($_POST[$setting]) > 0){
                $_SESSION['config'][$setting] = $_POST[$setting];
            }
            else{
                $label = str_replace('_', ' ', $setting);
                $this->errors[] = ucwords($label).' value not set';
                $rval = false;
            }
        }
        //Not mandatory, but set if defined
        if(isset($_POST['table_prefix'])){
            $_SESSION['config']['table_prefix'] = $_POST['table_prefix'];
        }
        return $rval;
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
        }
    }
    function db(){
        $dsn = 'mysql://'.$_POST['db_username'].':'.rawurlencode($_POST['db_password']).'@'.$_POST['db_host'].'/'.$_POST['db_database'];
            $db = NewADOConnection($dsn);
            if($db !== false){
                $this->db =& $db;
                //$this->createdatabase();
            }
            return true;
            /*else{
                $this->errors[] = 'Unable to connect to the database. The reported error was: '.$db->ErrorMsg();
            }*/
    }
    function installplugins(){
        include_once(LOQ_APP_ROOT.'includes/pluginhandler.class.php');
        define('T_PLUGINS',$_SESSION['config']['table_prefix'].'plugins');
        $ph = new pluginhandler($this->db);
        $ph->scan_for_plugins(LOQ_APP_ROOT.'plugins');
    }
    function writeconfig(){
        // Write config!
		echo "<h3>Writing config.php file</h3>";
		
		if (!isset($config['extra_config'])) $config['extra_config'] = '';
		
		$config_file = "<?php
        /*
        
           '||     '||'''|,  '||`
            ||      ||   ||   ||
            ||''|,  ||;;;;    ||  .|''|, .|''|,
            ||  ||  ||   ||   ||  ||  || ||  ||
           .||..|' .||...|'  .||. `|..|' `|..||
                                             ||
                  .7                      `..|'
        
        ** bBlog Weblog Software http://www.bblog.com/
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
        
        /* login details are stored in the database */
        
        
        
        /* MySQL details */
        
        // MySQL database username
        define('DB_USERNAME','".$config['mysql_username']."');
        
        // MySQL database password
        define('DB_PASSWORD','".$config['mysql_password']."');
        
        // MySQL database name
        define('DB_DATABASE','".$config['mysql_database']."');
        
        // MySQL hostname
        define('DB_HOST','".$config['mysql_host']."');
        
        // prefix for table names if you're installing
        // more than one copy of bblog on the same database
        // don't change this unless you know what you're doing.
        define('TBL_PREFIX','".$config['table_prefix']."');
        
        /* file and paths */
        
        // Full path of the directory where you've installed bBlog
        // ( i.e. the bblog folder )
        define('LOQ_APP_ROOT','".$config['path']."');
        
        /* URL config */
        
        // URL to your blog ( one folder below the 'bBlog' folder )
        // e.g, if your bBlog folder is at www.example.com/blog/bblog, your
        // blog will be at www.example.com/blog/
        define('BLOGURL','".$config['url']."');
        
        // URL to the bblog folder via the web.
        // Becasue if you're using clean urls and news.php as your BLOGURL,
        // we can't automatically append bblog to it.
        define('BBLOGURL',BLOGURL.'bblog/');
        
        // Clean or messy urls ? ( READ README-URLS.txt ! )
        //define('CLEANURLS',TRUE);
        //define('URL_POST','".$config['url']."item/%postid%/');
        //define('URL_SECTION','".$config['url']."section/%sectionname%/');
        
        
        ".$config['extra_config']."
        
        // ---- end of config ----
        // leave this line alone
        include LOQ_APP_ROOT.'inc/init.php';
        ?>";
        $fp = fopen('./config.php', 'w');
		fwrite($fp, $config_file);
		fclose($fp);
		echo '<p>Config file written. <input type="submit" name="continue" value="Next &gt;" /></p>';
		$step = 8;
		break;
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
