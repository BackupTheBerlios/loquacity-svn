<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Mario Delgado
 * @copyright &copy; 2007 Kenneth Power <telcor@users.berlios.de>, &copy; 2003  Mario Delgado <mario@seraphworks.com>
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
  * administer links
  */

function identify_admin_links () {

  $help = '<p>Links is just a way of managing links. This plugin allows you to add, edit and delete links.';

  return array (
    'name'           =>'links',
    'type'           =>'admin',
    'nicename'       =>'Links',
    'description'    =>'Edit Loquacity Links',
    'template' 	     =>'links.html',
    'authors'        =>'Mario Delgado <mario@seraphworks.com>, Eaden McKee <email@eadz.co.nz>',
    'licence'        =>'GPL',
    'help'           => $help
  );
}

function admin_plugin_links_run(&$loq) {
    if(isset($_GET['linkdo']))  { $linkdo = $_GET['linkdo']; }
    elseif(isset($_POST['linkdo'])) { $linkdo = $_POST['linkdo']; }
    else { $linkdo = ''; }
    $linkdo = strtolower($linkdo);
    switch($linkdo) {
        case "new":
			$link_name = $_POST['nicename'];
			$link_url = $_POST['url'];
			$link_cat = intval($_POST['category']);
			if(strlen($link_name) > 0 && strlen($link_url) > 0 && $link_cat > 0){
				$maxposition = $loq->_adb->GetOne("select MAX(position) from `".T_LINKS."`");
	            $position = $maxposition + 10;
				$stmt = $loq->_adb->Prepare('INSERT INTO `'.T_LINKS.'` VALUES(null, ?, ?, ?, ?)');
				$loq->_adb->Execute($stmt, array($link_name, $link_url, $link_cat, $postition));
			}
            break;
        case "delete" : // delete link
                $loq->_adb->Execute("delete from ".T_LINKS." where linkid=".$_POST['linkid']);
                break;
    
        case "save" : // update an existing link
                $loq->_adb->Execute("update ".T_LINKS."
                set nicename='".stringHandler::removeMagicQuotes($_POST['nicename'])."',
                url='".stringHandler::removeMagicQuotes($_POST['url'])."',
                category='".stringHandler::removeMagicQuotes($_POST['category'])."'
                where linkid=".$_POST['linkid']);
                break;
        case "up" :
            $loq->_adb->Execute("update ".T_LINKS." set position=position-15 where linkid=".$_POST['linkid']);
            reorder_links();
    
            break;
    
        case "down" :
            $loq->_adb->Execute("update ".T_LINKS." set position=position+15 where linkid=".$_POST['linkid']);
            reorder_links();
            break;
        default : // show form
                break;
        }
    if(isset($_GET['catdo']))  { $catdo = $_GET['catdo']; }
    elseif (isset($_POST['catdo'])) { $catdo = $_POST['catdo']; }
    else { $catdo = ''; }
    $catod = strtolower($catdo);
    switch($catdo) {
        case "new" :  // add new category
			$cat_name = $_POST['name'];
			if(strlen($cat_name) > 0){
				$stmt = $loq->_adb->Prepare('INSERT INTO `'.T_CATEGORIES.'` VALUES(null, ?)');
            	$loq->_adb->Execute($stmt, array($cat_name));
			}
            break;
    
        case "delete" : // delete category
            // have to remove all references to the category in the links
                $loq->_adb->Execute("update ".T_LINKS."
                set linkid=0 where linkid=".$_POST['categoryid']);
                // delete the category
                $loq->_adb->Execute("delete from ".T_CATEGORIES." where categoryid=".$_POST['categoryid']);
                break;
    
        case "save" : // update an existing category
                $loq->_adb->Execute("update ".T_CATEGORIES."
                set name='".$_POST['name']."'
                where categoryid=".$_POST['categoryid']);
                break;
    
        default : // show form
                break;
        }
    
    $rs = $loq->_adb->Execute("select * from ".T_CATEGORIES);
    if($rs !== false && !$rs->EOF){
        $loq->assign('ecategories',$rs->GetRows(-1));
    }
    $rs = $loq->_adb->GetAll("select * from ".T_LINKS." order by position");
    if(is_array($rs)){
        $loq->assign('elinks',$rs);
    }

}
function reorder_links () {
	global $loq;
	$i = 20;
    $rs = $loq->_adb->Execute("select * from ".T_LINKS." order by position");
    if($rs !== false && !$rs->EOF){
        while($link = $rs->fetchRow()){
            $loq->_adb->Execute("update ".T_LINKS." set position='$i' where linkid='{".$link['linkid']."}'");
            $i += 10;
        }
    }
}

?>