<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Installation
 * @author Samir Greadly <xushi.xushi@gmail.com>
 * @copyright Copyright &copy; 2006 Loquacity
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
 * Xushi :
 * Note, the only difference in the database between 0.7.4
 * and 0.7.6 is just the addition of VERSION in {pfx}config.
 * However, Some might still upgrade from 0.7.4 and
 * below, so i'll keep the 0.7.5 updates here with a check to
 * see if they're installed or not.
 *
**/

	// correct config.php path.
	if(file_exists("../config.php")) {
		include '../config.php';
	} else {
		include 'config.php';
	}
?>
<html>
<header>
<link rel="stylesheet" type="text/css" title="Main" href="../style/admin.css" media="screen" />
</header>
<body>
<body><div id="header">
<h1>Loquacity</h1>
<h2>Upgrading</h2>
</div>

<div style="width: 500px; margin-left: auto; margin-right: auto; margin-top: 80px;">
<?php
	echo '';
	
	// 0.7.5's old updates.. 
	
	//lets see if CHARSET and DIRECTION are already there..
	echo "Checking to see if 0.7.5's patches are installed...<br />";
	if (defined('C_CHARSET') && defined('C_DIRECTION') ){
		//no updates necessary :)
		echo "<p>Your database looks good and does not need 0.7.5's patches. :)</p>";
	}
	else{
		//we should add 2 values to db
		echo "Not found, patching now.<br />";
		$q = "INSERT INTO `".T_CONFIG."` (`id`, `name`, `value`) VALUES
		('', 'CHARSET', 'UTF-8'),
		('', 'DIRECTION', 'LTR')";
		
		//just do it
		$loq->_adb->Execute($q);
	}//else	



	// 0.7.6's updates (VERSION)
	
	echo "Now installing 0.7.6 upgrades<br /><br />";
	echo "Checking if VERSION already exists...<br />";
	
	$ver = $loq->get_var("select value from ".T_CONFIG." where name='VERSION'");
	$newVer = 0.76;
	if(isset($ver)) {
		// update
		echo "Found a previous version. Updating to 0.7.6 now<br /><br />";
		$loq->_adb->Execute("UPDATE ".T_CONFIG." SET VALUE='".$newVer."' WHERE `name`='VERSION'");
	} 
	else {
		// otherwise, write a new one 
		echo "No VERSION value found. Creating 0.76 now...<br /><br />";
		$loq->_adb->Execute("INSERT INTO `".T_CONFIG."` (`id`, `name`, `value`) VALUES
			('', 'VERSION', '$newVer')");
	}
		
		
	
   	echo "<h3>Done.</h3>";
	// All Done
	echo "<p>The upgrade finished successfully.<br /><br />
		<h3><u>Security</u></h3>
		<p>Now, you need to do 3 things to finish off
		<ol>
	    	<li>Delete install.php and the install folder</li>
	    	<li>chmod -rw config.php, so that it is not writable by the webserver</li>
	    	<li>When you have done that, you may <a href='../index.php?b=options'>Login to Loquacity.</a></li>	
		</ol></p>";
?>
</div>

<div id="footer">
<a href="http://www.loquacity.info" target="_blank">
Loquacity</a> &copy; 2006 <a href="mailto:admin@loquacity.info">Kenneth Power</a> &amp; <a href="index.php?b=about" target="_blank">The Team</a>
</div>

</body>
</html>