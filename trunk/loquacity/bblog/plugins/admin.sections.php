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

function admin_plugin_sections_run(&$bBlog) {
    // Again, the plugin API needs work.
    if(isset($_GET['sectdo']))  { $sectdo = $_GET['sectdo']; }
    elseif(isset($_POST['sectdo'])) { $sectdo = $_POST['sectdo']; }
    else { $sectdo = ''; }

    switch($sectdo) {
        case 'new' :  // sections are being editied
            $nicename = stringHandler::removeMagicQuotes($_POST['nicename']);
            $urlname = stringHandler::removeMagicQuotes($_POST['urlname']);
            $bBlog->_adb->Execute("insert into ".T_SECTIONS." set nicename=".$bBlog->_adb->quote($nicename).", name=".$bBlog->_adb->quote($urlname));
            $insid = $bBlog->_adb->insert_id();
            break;
        case "Delete" : // delete section
            // have to remove all references to the section in the posts
            $sname = stringHandler::removeMagicQuotes($_POST['sname']);
            $sect_id = $bBlog->section_ids_by_name[$sname];
            if($sect_id > 0) {
                $ph = $bBlog->_ph;
                $posts_in_section_q = $ph->make_post_query(array("sectionid"=>$sect_id));
                $posts_in_section = $ph->get_posts($posts_in_section_q,TRUE);
                if($posts_in_section) {
                    foreach($posts_in_section as $post) {
                        unset($tmpr);
                        $tmpr = array();
                        $tmpsections = explode(":",$post->sections);
                        foreach($tmpsections as $tmpsection) {
                            if($tmpsection != $sect_id) $tmpr[] = $tmpsection;
                        }
                        $newsects = implode(":",$tmpr);
                        // update the posts to remove the section
                        $bBlog->_adb->Execute("update ".T_POSTS." set sections='$newsects' where postid={$post->postid}");
                    } // end foreach ($post_in_section as $post)
                } // end if($posts_in_section)
                // delete the section
                $bBlog->_adb->Execute("delete from ".T_SECTIONS." where sectionid=$sect_id");
            } // else show error
        case "Save" :
            $sect_id = $bBlog->sect_by_name[$_POST['sname']];
            if($sect_id < 1) break;
            $sql = "update ".T_SECTIONS ." set nicename='".stringHandler::clean($_POST['nicename'])."' where sectionid='$sect_id'";
            $bBlog->_adb->Execute($sql);
            break;
        default : // show form
            break;
    }
    $bBlog->get_sections();
    $bBlog->assign('esections',$bBlog->sections);
}
?>