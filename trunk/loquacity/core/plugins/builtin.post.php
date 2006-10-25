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
 * Handles posting an entry
 */

function identify_admin_post () {
    return array (
    'name'           =>'post',
    'type'             =>'builtin',
    'nicename'     =>'Post',
    'description'   =>'Post in your blog',
    'authors'        =>'Eaden McKee <eadz@bblog.com>',
    'licence'         =>'GPL',
    'help'            =>''
  );
}


$loq->assign('form_type','post'); // used in the template post_edit.html
$loq->assign('commentsallowvalue', " checked='checked' ");
if((isset($_POST['newpost'])) && ($_POST['newpost'] == 'true')) {    // we have a poster
    // make the data sql save
    //$post = prep_new_post();
    $ph =& $loq->_ph;
    
    $res = $ph->new_post($_POST);
    if(is_int($res)) {
        $loq->assign('post_message',"Post #$res Added :)");
        if(strlen(C_PING)>0) {
            include LOQ_APP_ROOT.'libs/rpc.php'; // include stuff needed to ping
            register_shutdown_function('ping'); // who wants to wait for 4
            // requests before the page loads ?
        }
        
        if ((isset($_POST['send_trackback'])) && ($_POST['send_trackback'] == "TRUE")) {
            // send a trackback
            include "./trackback.php";
            send_trackback($loq->_get_entry_permalink($res), $_POST['title_text'], $_POST['excerpt'], $_POST['tburl']);
        }

      }
      else{
          $loq->assign('post_message',"Sorry, error adding post: ".$ph->lastError());
      }
}

// get modifiers
$loq->get_modifiers();
$loq->assign('selected_modifier',C_DEFAULT_MODIFIER);

if(C_DEFAULT_STATUS == 'draft') $loq->assign('statusdraft','checked="checked"');
else $loq->assign('statuslive','checked="checked"');

if ((isset($_REQUEST['popup']) && ($_REQUEST['popup'] == 'true'))) {
	include 'includes/bookmarkletstuff.php';
	$loq->display('popuppost.html');
} else {
	$loq->display('post.html');
}

////
// !makes sure post data is sql safe
// and in a nice format
function prep_new_post () {
    $post->title = stringHandler::removeMagicQuotes($_POST['title_text']);
    $post->body  = stringHandler::removeMagicQuotes($_POST['body_text']);

    // there has to be abetter way that this but i'm tired.
    if(!isset($_POST['modifier'])) $post->modifier = C_DEFAULT_MODIFIER;
    else $post->modifier = stringHandler::removeMagicQuotes($_POST['modifier']);

    if(!isset($_POST['pubstatus'])) $post->status = C_DEFAULT_STATUS;
    else $post->status = stringHandler::removeMagicQuotes($_POST['pubstatus']);

    if (isset($_POST['sections'])) 
    {
    	$_tmp_sections = (array) $_POST['sections'];
    }
    else
    {
    	$_tmp_sections = null;
    }
    
    $post->sections = array();
    $post->providing_sections = TRUE; // this is so that Loquacity knows to delete sections if there are none.
    
    if (!is_null($_tmp_sections)) foreach ($_tmp_sections as $_tmp_section) if(is_numeric($_tmp_section)) $post->sections[] = $_tmp_section;

    if ((isset($_POST['hidefromhome'])) && ($_POST['hidefromhome'] == 'hide')) { $hidefromhome='hide'; }
    else { $hidefromhome='donthide'; }
    
    $post->hidefromhome = $hidefromhome;
    $post->allowcomments = $_POST['commentoptions'];

    if (isset($_POST['disallowcommentsdays'])) { $disdays = (int) $_POST['disallowcommentsdays']; } else { $disdays = 0; }
    
    $time = (int) time();
    $autodisabledate = $time + $disdays * 3600 * 24;
    
    $post->autodisabledate = $autodisabledate;

    return $post;
}
?>