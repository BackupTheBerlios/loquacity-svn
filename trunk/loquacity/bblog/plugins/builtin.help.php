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
function identify_admin_help () {
  return array (
    'name'           =>'help',
    'type'           =>'admin',
    'nicename'       =>'Help',
    'description'    =>'Displays Help',
    'authors'         =>'Eaden McKee',
    'licence'         =>'GPL'
  );
}
if(is_numeric($_GET['pid']) or strlen($_GET['mod'])>0) {
    $bBlog->assign('pluginhelp',TRUE);
	if($_GET['mod']){
        $pluginrow = array();
        $rs = $bBlog->adb->Execute("select * from ".T_PLUGINS." where name='".$_GET['mod']."' and type='modifier'");
        if($rs !== false && !$rs->EOF){
            $pluginrow = $rs->FetchRow();
        }
    }
	else{
        $rs = $bBlog->_adb->Execute("select * from ".T_PLUGINS." where id='".$_GET['pid']."'");
        if($rs !== false && !$rs->EOF){
            $pluginrow = $rs->FetchRow();
        }
    }
	$bBlog->assign("title","Help: ".$pluginrow['type']." : ".$pluginrow['nicename']);
	$bBlog->assign("helptext",$pluginrow['help']);
	$bBlog->assign("type",$pluginrow['type']);
	$bBlog->assign("nicename",$pluginrow['nicename']);
	$bBlog->assign("description",$pluginrow['description']);
	$bBlog->assign("authors",$pluginrow['authors']);
	$bBlog->assign("license",$pluginrow['license']);

}
elseif($_GET['modifierhelp']) {
    $bBlog->assign('title','Modifier Help');
	$bBlog->assign('inline',TRUE);
	$helptext = "<p>Modifiers are an easy way to enable you to make links and other web features without knowing html. There are a few to choose fshowcloserom, select one to get instructions.</p><ul class='form'>";
    $rs = $bBlog->_adb->Execute("select * from ".T_PLUGINS." where type='modifier' order by nicename");
    if($rs !== false && !$rs->EOF){
        while($mod = $rs->FetchRow()){
            $helptext .= "<li><a href='index.php?b=help&amp;inline=true&amp;pid={$mod['id']}'>{$mod['nicename']}</a> - {$mod['description']}</li>";
    }
	$helptext .="</ul>";
	$bBlog->assign('helptext',$helptext);
    }
    else {
        $bBlog->assign("title","Help");
        $bBlog->assign("helptext",'Visit the <a href="http://www.bblog.com/docs/" target="_blank">bBlog online documentation</a> or the <a href="http://www.bBlog.com/forum.php" target="_blank">bBlog forum</a> for help.');
    }
}
    $bBlog->display("help.html");
?>