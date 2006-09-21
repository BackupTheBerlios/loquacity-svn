<?php

class installinstall extends installbase{
	function installinstall(){
        installbase::installbase();
        //$this->template = 'bob';
	}
    function __init(){
        if($this->checkConfigSettings()){
            $dsn = 'mysql://'.$_POST['db_username'].':'.rawurlencode($_POST['db_password']).'@'.$_POST['db_host'].'/'.$_POST['db_database'];
            if(($db = NewADOConnection($dsn)) !== false){
                $this->db =& $db;
            }
        }
        if(isset($this->errors) && count($this->errors) > 0){
            $this->template = 'configuration.html';
            $this->loadconfiguration();
        }
        else{
            $this->template = '';
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
            //var_dump(strlen($_POST[$setting]) > 0);
            if(isset($_POST[$setting]) && strlen($_POST[$setting]) > 0){
                $_SESSION['config'][$setting] = $_POST[$setting];
            }
            else{
                $label = str_replace('_', ' ', $setting);
                $this->errors[] = ucwords($label).' value not set';
                $rval = false;
            }
        }
        return $rval;
    }
    function installplugins(){
        echo "<h3>Loading Plugins</h3>";
		$newplugincount = 0;
		$newpluginnames = array();
		$plugin_files=array();
		$dir="./bBlog_plugins";
		$dh = opendir( $dir ) or die("couldn't open directory");
		while ( ! ( ( $file = readdir( $dh ) ) === false ) ) {
			if(substr($file, -3) == 'php') $plugin_files[]=$file;
		}
		closedir( $dh );
		echo "<table border='0' class='list'>";
		foreach($plugin_files as $plugin_file) {
			$far = explode('.',$plugin_file);
			$type = $far[0];
			$name = $far[1];
			if($type != 'builtin') {
				include_once './bBlog_plugins/'.$plugin_file;
				$func = 'identify_'.$type.'_'.$name;
				if(function_exists($func)) {
					$newplugin = $func();
					
					if (!isset($newplugin['template'])) { $newplugin['template'] = ""; }
					
					$q = "insert into ".$config['table_prefix']."plugins set
					`type`='".$newplugin['type']."',
					`name`='".$newplugin['name']."',
					nicename='".$newplugin['nicename']."',
					description='".addslashes($newplugin['description'])."',
					template='".$newplugin['template']."',
					help='".addslashes($newplugin['help'])."',
					authors='".addslashes($newplugin['authors'])."',
					licence='".$newplugin['licence']."'";
			 		$db->Execute($q);
			 		echo '<tr><td>'.$newplugin['nicename'].'</td><td>..........Loaded</td></tr>';

				} // end if function exists
			} // end if
		} // end foreach
		echo "</table>";
		echo '<p>Done. <input type="submit" name="submit" value="Next &gt;" />';
		$func = 'upgrade_from_'.$config['upgrade_from'].'_post';
		if($config['install_type'] == 'upgrade' && function_exists($func)) $step = 6;
			else $step = 7;
		break;
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
        define('BBLOGROOT','".$config['path']."');
        
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
        include BBLOGROOT.'inc/init.php';
        ?>";
        $fp = fopen('./config.php', 'w');
		fwrite($fp, $config_file);
		fclose($fp);
		echo '<p>Config file written. <input type="submit" name="continue" value="Next &gt;" /></p>';
		$step = 8;
		break;
    }
    function createdatabase(){
        //For MySQL there are three main versions to worry about:
        // 4.0, 4.1 and 5.0
        //4.1+ has real charset support, while <4.1 doesn't
        // do sql.

		$q = array();
        $pfx = $config['table_prefix'];



			$i=0;
			echo "<h3>Creating tables</h3><p>";
			$db = new db($config['mysql_username'],$config['mysql_password'],$config['mysql_database'],$config['mysql_host']);
			foreach($q as $q2do) {
				$i++;
				echo $i." ";
				//echo "<pre>$q2do</pre>";
				$db->Execute($q2do);
			}
			echo ' done.<input type="submit" name="submit" value="Next &gt;" /></p>';
			$step = 5;

		break;
    }
}
