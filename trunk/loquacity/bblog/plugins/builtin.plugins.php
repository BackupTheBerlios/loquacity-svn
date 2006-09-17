<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Eaden McKee, http://www.bblog.com
 * @copyright &copy; 2003  Eaden McKee <email@eadz.co.nz>
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
 * this is supposed to be a GUI for plugins,
 * need some help with some sort of api,
 * in the mean time we'll put them in an array.
 */
 
$show_plugin_menu = TRUE;
$plugin_ar  = array();

function identify_admin_plugins () {
  return array (
    'name'           =>'plugins',
    'type'           =>'builtin',
    'nicename'       =>'Plugins',
    'description'    =>'Display information about, and run plugins',
    'authors'         =>'Eaden McKee',
    'licence'         =>'GPL'
  );
}
// PLUGINS :

function scan_for_plugins () {
	global $bBlog;
	$currentPlugins = currentPlugins($bBlog->_adb);
	$dir=dirname(__FILE__);
	$scanned = scanPluginDir($dir);
	$installed = 0;
	foreach($scanned as $type => $typeList){
		foreach($typeList as $ind=>$member){
			if((!isset($currentPlugins[$type])) || (isset($currentPlugins[$type]) && !in_array($member, $currentPlugins[$type]))){
				if(installPlugin($bBlog->_adb, $type, $member)){
					$installed++;
				}
			}
		}
	}
	return $installed;
}
	
function installPlugin($db, $type, $plugin){
	$loader = 'identify_'.$type.'_'.$plugin;
	if(function_exists($loader)){
		$newplugin = $loader();
		$sql = 'INSERT INTO `'.T_PLUGINS.'`(type,name,nicename,description,template,help,authors,licence) VALUES("'.$type.'","'.$newplugin['name'].'","'.$newplugin['nicename'].'","'.$newplugin['description'].'",	"'.$newplugin['template'].'","'.$newplugin['help'].'","'.$newplugin['authors'].'", "'.$newplugin['license'].'")';
		//$db->debug = true;
		if($db->Execute($sql)){
			return true;
		}
	}
	return false;
}	

if(isset($_POST['scan'])) $np = scan_for_plugins();

if(isset($_POST['scan_refresh'])) {
	$np = scan_for_plugins();
	$bBlog->assign('np',"<b style='color: red;'>$np</b><br />");
}



$rs = $bBlog->_adb->Execute("select * from ".T_PLUGINS." order by type");
if($rs !== false && !$rs->EOF){
    while($plugin = $rs->FetchRow()){
        $plugins[$plugin['type']][$plugin['name']]= array(
            "name"=>$plugin['name'],
            "id" => $plugin['id'],
            "type"=>$plugin['type'],
            "displayname"=>$plugin['nicename'],
            "description"=>$plugin['description'],
            "file"=>$plugin['type'].".".$plugin['name'].".php",
            "template"=>$plugin['template'],
            "author"=>$plugin['authors'],
            "licence"=>$plugin['licence'],
            "help"=>$plugin['help']
        );
       $plugin_ar[] = $plugins[$plugin['type']][$plugin['name']];
    }
}
// other plugin :




$p=FALSE;
if($_GET['p']) $p = $_GET['p'];
if($_POST['p']) $p = $_POST['p'];

if($p && is_array($plugins['admin'][$p])) { // successful call to plugin
	$show_plugin_menu = FALSE;
	$bBlog->assign('plugin',$plugins['admin'][$p]);
	$bBlog->assign('plugin_template','plugins/'.$plugins['admin'][$p]['template']);
	$bBlog->assign('title',$plugins['admin'][$p]['displayname']);
	include_once('plugins/'.$plugins['admin'][$p]['file']);
	$func = "admin_plugin_".$p."_run";
	$func($bBlog);
}

$bBlog->assign('plugin_ar',$plugin_ar);
$bBlog->assign('show_plugin_menu',$show_plugin_menu);


$bBlog->display("plugins.html");

function scanPluginDir($dir){
	$pluginList = array();
	$dh = opendir($dir);
	while((($file = readdir( $dh )) !== false )){
		if($file === 'smarty'){
			$list1 = scanPluginDir($dir . DIRECTORY_SEPARATOR . $file);
			foreach($list1 as $type => $plugins){
				if(is_array($pluginList[$type])){
					array_merge($pluginList[$type],  $plugins);
				}
				else{
					$pluginList[$type] = $plugins;
				}
			}
		}
		else if(substr($file, -3) == 'php'){
			$parts = explode('.',$file);
			if($parts[0] !== 'builtin'){
				if(isValidPlugin($dir, $file)){
					$pluginList[$parts[0]][] = $parts[1];
				}
			}
		}
	}
	closedir( $dh );
	return $pluginList;
}

function currentPlugins(&$db){
	$current = array();
	$rs = $db->Execute("select type, name from ".T_PLUGINS);
	if($rs !== false && !$rs->EOF){
        	while($plugin = $rs->FetchRow()){
            		$current[$plugin['type']][] = $plugin['name'];
        	}
    	}
	return $current;
}

function isValidPlugin($dir, $file){
	$parts = explode('.', $file);
	if(file_exists($dir. DIRECTORY_SEPARATOR . $file)){
		include_once($dir. DIRECTORY_SEPARATOR . $file);
		$loader = 'identify_'.$parts[0].'_'.$parts[1];
		if(function_exists($loader)){
			return true;
		}
	}
	return false;
}
?>