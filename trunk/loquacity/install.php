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
	// using sessions becasue it makes things easy
	session_start();
    $smarty = new Smarty();
    $smarty->template_dir = LOQ_INSTALLER.'/templates';
    $smarty->compile_dir = '/tmp'; //This probably won't work on Windows

	// start install all over, forget everything.
	if (isset($_GET['reset'])) {
		unset($config);
		unset($step);
		@session_destroy();
		header("Location: install.php");
		exit;
	}

	$step =& $_SESSION['step'];
	$config =& $_SESSION['config'];

	if(!isset($_SESSION['step'])) $step=0;

	
	if($step > 2) {
		$db = new db($config['mysql_username'], $config['mysql_password'], $config['mysql_database'], $config['mysql_host']);
	}

	if(isset($config['upgrade_from'])) {
		if(file_exists('./install/upgrade.'.$config['upgrade_from'].'.php')) {
			include './install/upgrade.'.$config['upgrade_from'].'.php';
		} else {
			echo "<h3>Error</h3>";
			echo "<p>You have chosen an upgrade option, but the upgrade file (  install/upgrade.".$config['upgrade_from'].".php ) is missing";
			include 'install/footer.php';
			exit;
		}
	}
	switch ($step) {
		case 0:
            $smarty->assign('step', 0);
			$smarty->display('welcome.html');
		break;
		
		// Case 1: Find out if the user is installing a new version,
		// or upgrading from another one.
		
		case 1:
			if ((isset($config['install_type'])) && ($config['install_type'] == 'upgrade')) {
				echo "<h3>Upgrading</h3>";
				$intro_func = 'upgrade_from_'.$config['upgrade_from'].'_intro';
				if(function_exists($intro_func)) $intro_func();
			}
			$test = check_writable();
			if($test) echo "<p>Great, all working. <input type='submit' name='continue' value='Click here to continue' /></p>";
			else echo "<p>Please fix above errors, then <input type='submit' name='continue' value='Click here to try again' /></p>";
		break;
		
		//Case 2, If user is installing from scratch, 
		// provide the DB & Blog settings page
		case 2:
			?>
			<h3>Database and blog settings</h3>
			<?php 
			if (isset($message)) {
				echo $message; 
			}
			?>
			<p>Please fill in the config settings below</p>

			<table border="0" class='list' cellpadding="4" cellspacing="0">
			<tr>
  <td colspan="3"><h4>General Config</h4></td>
</tr>
<tr bgcolor="#ddd">
  <td width="33%">Blog Name</td>
  <td width="200"><input type="text" name="blogname" value="<?php echo $config['blogname']; ?>" /></td>
  <td  width="33%" class='si'>A short name for your blog, e.g. "My Blog"</td>
</tr>
<tr bgcolor="#eeeeee">
  <td width="33%" bgcolor="#eeeeee">Blog Description</td>
  <td width="200"><input type="text" name="blogdescription" value="<?php echo $config['blogdescription']; ?>"/>
  </td>
  <td  width="33%" class='si'>A short descriptive subtitle e.g. "A blog about fish"</td>
</tr>
<tr bgcolor="#ddd">
  <td width="33%" bgcolor="#eee">Full Name
    </td>
  <td><input type="text" name="fullname" value="<?php echo $config['fullname']; ?>"/></td>
  <td class='si'>The owners full name </td>
</tr>
<tr bgcolor="#ddd">
  <td width="33%" bgcolor="#eeeeee">Username</td>
  <td width="200"><input type="text" name="username" value="<?php echo $config['username']; ?>"/>
  </td>
  <td width="33%" class='si'>The username you want to use to log in to bBlog</td>
</tr>
<tr bgcolor="#eeeeee">
  <td width="33%" bgcolor="#eeeeee">Password</td>
  <td width="200"><input type="password" name="password" value="<?php echo $config['password']; ?>"/></td>
  <td width="33%" class='si'>The password you want to use to log in to bBlog</td>
</tr>
<tr bgcolor="#ddd">
  <td width="33%" bgcolor="#eee">Re-enter Password</td>
  <td width="200"><input type="password" name="secondPassword" value="<?php echo $config['secondPassword']; ?>"/></td>
  <td width="33%" class='si'>Please re-enter the password.</td>
</tr>
<tr bgcolor="#eee">
  <td width="33%" bgcolor="#eeeeee">Email Address</td>
  <td width="200"><input type="text" name="email" value="<?php echo $config['email']; ?>"/></td>
  <td width="33%" class='si'>Where to send notifications of comments</td>
</tr>
<tr>
  <td colspan="3"><h4>MySQL Settings</h4></td>
</tr>
</td>
<tr bgcolor="#ddd">
  <td width="33%">MySQL Username</td>
  <td width="200"><input type="text" name="mysql_username" value="<?php echo $config['mysql_username']; ?>"/></td>
  <td width="33%" class='si'>Your MySQL username</td>
</tr>
<tr bgcolor="#eeeeee">
  <td width="33%">MySQL Password</td>
  <td width="200"><input type="password" name="mysql_password" value="<?php echo $config['mysql_password']; ?>" /></td>
  <td width="33%" class='si'>Your MySQL password</td>
</tr>
<tr bgcolor="#ddd">
  <td width="33%">MySQL Database Name</td>
  <td width="200"><input type="text" name="mysql_database" value="<?php echo $config['mysql_database']; ?>"/></td>
  <td width="33%" class='si'>Your MySQL database name<br>( usually the same as your username )</td>
</tr>
<tr bgcolor="#eeeeee">
  <td width="33%">Mysql Host</td>
  <td width="200"><input type="text" name="mysql_host" value="<?php echo $config['mysql_host']; ?>" /></td>
  <td width="33%" class='si'>The MySQL host name is usually 'localhost'</td>
</tr>
<tr bgcolor="#ddd">
  <td width="33%">Table Prefix</td>
  <td width="200"><input type="text" name="table_prefix" value="<?php echo $config['table_prefix']; ?>" /></td>
  <td width="33%" class='si'>Prefix of tables ( usually bB_ )</td>
</tr>
<tr>
  <td colspan="3"><h4>Server Settings</h4></td>
</tr>
<tr bgcolor="#ddd">
  <td width="33%">Url to your blog</td>
  <td width="200"><input type="text" name="url" value="<?php echo $config['url']; ?>" /></td>
  <td width="33%" class='si'>The full URL to your blog</td>
</tr>
<tr bgcolor="#eeeeee">
  <td width="33%">Path to bBlog</td>
  <td width="200"><input type="text" name="path" value="<?php echo $config['path']; ?>" />
  </td>
  <td width="33%" class='si'>The full UNIX path to the bblog directory</td>
</tr>
</table>
<p><input type="submit" name="submit" value="Next &gt;" />
<?php
		break;



	// Case 3: However, if user is upgrading from a 
	// previous install, then run the upgrade script.
	
	case 3:
	
		// add section here to extract details from the DB,
		// since they already exist and we don't need to 
		// ask the user about them allover again.
		
		//$config['blogname'] = ...
		// on hold.. its pointless to have this here now... 
		
		// Execute the upgrading function
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
	break;




	// Case 4: create the new tables, based on a fresh
	// install of bblog.
	
	case 4:
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



	// Case 5: Scan and update all the plugins 

	case 5:
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
		
		
	
	// Case 6: post-install upgrade stuff, 
	// such as getting config to write, or giving hints.
		
	case 6:
		$func = 'upgrade_from_'.$config['upgrade_from'].'_post';
		$func();
		break;
	
	
	
	// Case 7 : Finally, create and write the config.php file.
	
	case 7:
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



		// Case 8: Print out a few good messages to the user :)

		case 8:
			echo "<h3>All Done!</h3>";
			echo "<p>Install finished, almost....
		<h3>Security</h3>
		<p>Now, you need to do 3 things to finish off
<ol>
		    <li>Delete install.php</li>
		    <li>delete the install folder</li>
		    <li>Chmod the config.php so that it is not writable by the webserver</li>
		    <li>When you have done that, you may <a href='index.php?b=options'>Login to bBLog. Be sure to visit the Options page to set your email address and other options.</a></li>
</ol></p>";
			break;

}

#include 'install/footer.php';
function check_writable() {
	$ok = TRUE;
	if(is_writable("./cache")) {
		echo "./cache is writeable<br />";
	} else {
		echo "<span style='color:red;'>./cache is NOT writable</span><br />";
		$ok = FALSE;
	}

	if(is_writable("./compiled_templates")) {
		echo "./compiled_templates is writeable<br />";
	} else {
		echo "<span style='color:red;'>./compiled_templates is NOT writable</span><br />";
		$ok = FALSE;
	}
	
	if(is_writable("./cache/favorites.xml")) {
		echo "./cache/favorites.xml is writeable<br />";
	} else {
		echo "<span style='color:red;'>./cache/favorites.xml is NOT writable</span><br />";
		$ok = FALSE;
	}
	
	if(is_writable("./config.php")) {
		echo "./config.php is writeable<br />";
	} else {
		echo "<span style='color:red;'>./config.php is NOT writable</span><br />";
		$ok = FALSE;
	}

	return $ok;

}
?>
