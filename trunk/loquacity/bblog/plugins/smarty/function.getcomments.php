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
function identify_function_getcomments () {
    $help = '<p>Provides threaded comments. See the default templates for usage examples.';

    return array (
        'name'           =>'getcomments',
        'type'             =>'function',
        'nicename'     =>'Get Comments',
        'description'   =>'Gets Comments and trackbacks for a post and threads them',
        'authors'        =>'Eaden McKee <eaden@eadz.co.nz>',
        'licence'         =>'GPL',
        'help'   => $help
    );
}
function smarty_function_getcomments($params, &$bBlog) {
	$assign="comments";
	$postid=$bBlog->show_post;
	$replyto = $_REQUEST['replyto'];
    if($postid > 0 ){
        $ch = $bBlog->get_comment_handler($postid);
    }
    else{
        $ch = $bBlog->get_comment_handler($_REQUEST['postid']);
    }
    prep_form($bBlog, $postid, $_REQUEST['replyto']);
    // are we posting a comment ?
    if($_POST['do'] == 'submitcomment' && is_numeric($_POST['comment_postid'])) { // we are indeed!
        if(is_numeric($_POST['replytos'])){
            $rt = $_POST['replytos'];
        }
        else{
            $rt =  false;
        }
        $ch->new_comment($bBlog->_ph->get_post($_POST['comment_postid'], false, true),$rt, $_POST);
    }
               
    // get the comments.
    
    /* start loop and get posts*/
    
    $rt = false;
    if(is_numeric($_GET['replyto'])) {
        $rt = $_GET['replyto'];
        $cs = $ch->get_comment($_REQUEST['postid'],$rt);
    } else {
        $cs = $ch->get_comments(true);
    }
    /* assign loop variable */
    $bBlog->assign($assign, $cs);
}

function prep_form(&$bBlog, $postid, $replyto){
    //first, assign the hidden fields
    $commentformhiddenfields = '<input type="hidden" name="do" value="submitcomment" />';
    $commentformhiddenfields .='<input type="hidden" name="comment_postid" value="'.$postid.'" />';
    if(is_numeric($replyto)) {
          $commentformhiddenfields .= '<a name="commentform"></a><input type="hidden" name="replytos" value="'.$replyto.'" />';
    }
    $ph = $bBlog->_ph;
    $bBlog->assign("commentformhiddenfields",$commentformhiddenfields);
    $bBlog->assign("commentformaction",$ph->get_post_permalink($postid));
    
}

?>