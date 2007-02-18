<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.$loq->/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Eaden McKee, http://www.bblog.com
 * @copyright &copy; 2007 Kenneth Power <telcor@users.berlios.de>, &copy; 2003  Eaden McKee <email@eadz.co.nz>
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.loquacity.$loq->
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
 * administer sections
 */

function identify_admin_sections () {
    $help = '<p>Sections are just a way of organizing posts. This plugin allows you to edit and delete sections.
    When you make or edit a post, you can choose which sections it goes it.';
    return array (
        'name'           =>'sections',
        'type'             =>'admin',
        'nicename'     =>'Sections',
        'description'   =>'Edit Sections',
        'template' 	=> 'sections.html',
        'authors'        =>'Eaden McKee <eadz@bblog.com>',
        'licence'         =>'GPL',
        'help'            => $help
    );
}

function admin_plugin_sections_run(&$loq) {
    // Again, the plugin API needs work.
	$action = (isset($_POST['action'])) ? strtolower($_POST['action']) : '';
    switch($action){
        case 'new' :  // sections are being editied
            $nicename = $_POST['nicename'];
            $urlname = $_POST['urlname'];
            $loq->_adb->Execute("insert into ".T_SECTIONS." set nicename=".$loq->_adb->quote($nicename).", name=".$loq->_adb->quote($urlname));
            $insid = $loq->_adb->insert_id();
            break;
        case 'delete':
            // have to remove all references to the section in the posts
			$sectionid = intval($_POST['id']);
            if($sect_id !== 0) {
                $ph = $loq->_ph;
                $posts = $ph->getBySection($sectionid);
                if(!$posts->EOF){
                    while($posts->EOF === FALSE) {
                    	$s = $posts->fields['sections'];
                    	$s = str_replace($sectionid.':', '', $s);
                    	if(strlen($s) == 1){
                    		//Handles the case where the post was only assigned to this section
                    		$s = '::';
                    	}
                    	$loq->_adb->Execute('update `'.T_POSTS.'` set sections='.$s.' where postid='.$post->fields['id']);
                        $posts->MoveNext();
                    }
                }
                $loq->_adb->Execute("delete from `".T_SECTIONS."` where sectionid=$sectionid");
            }
			break;
        case 'edit':
        	$sectionid = intval($_POST['id']);
            if($sectionid < 1) break;
            $sql = "update `".T_SECTIONS ."` set nicename='".$_POST['nicename']."' where sectionid=$sectionid";
            $loq->_adb->Execute($sql);
            break;
        default : // show form
            break;
    }
    $loq->get_sections();
    $loq->assign('esections',$loq->sections);
}
?>