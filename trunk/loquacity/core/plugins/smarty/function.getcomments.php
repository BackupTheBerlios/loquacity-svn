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
        'name'		=>'getcomments',
        'type'		=>'function',
        'nicename'	=>'Get Comments',
        'description'	=>'Gets Comments and trackbacks for a post and threads them',
        'authors'	=>'Eaden McKee <eaden@eadz.co.nz>',
        'licence'	=>'GPL',
        'help'		=> $help
    );
}
function smarty_function_getcomments($params, &$loq) {
	$assign="comments";
	$postid=$loq->show_post;
	$replyto = null;
	$comment = null;
	if(isset($_POST['replyto']) && ctype_digit($_POST['replyto']) ){
		$replyto = intval($_POST['replyto']);
	}
	
	$ch = $loq->get_comment_handler();
	if(isset($_POST['do']) && $_POST['do'] == 'submitcomment' && ctype_digit($_POST['comment_postid'])) {
		if( $ch->new_comment( $loq->_ph->get_post($_POST['comment_postid']),$replyto, $_POST) === FALSE ){
			$loq->assign( 'message', $ch->errors('html') );
		};
	}
	if( isset( $_GET['replyto'] ) && ctype_digit( $_GET['replyto'] ) ){
		$replyto = intval($_GET['replyto']);
		$comment = $ch->getComment($replyto, 'html');
		$comment = $comment[0]['body'];
	}
	
	$comments = $ch->getComments($postid, 'html', 'thread');
	prep_form($loq, $postid, $replyto, $comment );
	$loq->assign($assign, $comments);
}

function prep_form(&$loq, $postid, $replyto, $comment){
    //first, assign the hidden fields
    $commentformhiddenfields = "<input type=\"hidden\" name=\"do\" value=\"submitcomment\" />\n";
    $commentformhiddenfields .= "<input type=\"hidden\" name=\"comment_postid\" value=\"".$postid."\" />\n";
    if(ctype_digit($replyto)) {
          $commentformhiddenfields .= "<input type=\"hidden\" name=\"replyto\" value=\"".$replyto."\" />\n";
    }
    if( !is_null( $comment ) ){
	    $commentformhiddenfields .= "<div id=\"parentcomment\"><strong>Replying to</strong>\n";
	    $commentformhiddenfields .= "<blockquote>".$comment."</blockquote>\n";
	    $commentformhiddenfields .= "</div>\n";
    }
    $ph = $loq->_ph;
    $loq->assign("commentformhiddenfields",$commentformhiddenfields);
    $loq->assign("commentformaction",$ph->get_post_permalink($postid));
    
}

?>