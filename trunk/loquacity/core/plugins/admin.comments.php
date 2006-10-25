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
 * administer comments
 */

function identify_admin_comments () {
    return array (
    'name'           =>'comments',
    'type'             =>'admin',
    'nicename'     =>'Comments',
    'description'   =>'Remove, Approve or Edit comments',
    'authors'        =>'Eaden McKee <eadz@bblog.com>',
    'licence'         =>'GPL',
    'template' 	=> 'comments_admin.html',
    'help'    	=> ''
  );
}

function admin_plugin_comments_run(&$loq) {
    // Again, the plugin API needs work.
    $commentAmount = (isset($_POST['commentsQuantity'])) ? intval($_POST['commentsQuantity']) : 50;
    $articles = null;
    if(isset($_POST['commentsPosts'])){
        $articles = ($_POST['commentsPosts'] === 'All') ? null : intval($_POST['commentsPosts']);
    }

    $commentdo = (isset($_POST['commentdo'])) ? $_POST['commentdo'] : '';
    switch($commentdo) {
        case "Delete" : // delete comments
            if(is_array($_POST['commentid'])){
                foreach($_POST['commentid'] as $key=>$val){
                    deleteComment($loq, $val);
                }
            }
            break;
        case "Edit" :
            $commentid = intval($_GET['editComment']);
            $postid = intval($_GET['postid']);
            editComment($loq, $commentid, $postid);
            break;
        case "editsave" :
          saveEdit($loq);
          break;
        case "Approve":
            if(is_array($_POST['commentid'])){
                foreach($_POST['commentid'] as $key=>$val)
                    $loq->_adb->Execute("UPDATE ".T_COMMENTS." SET onhold='0' WHERE commentid='".intval($val)."'");
            }
            break;
        case "filter":
        default : // show form
          break;
    }

    retrieveComments($loq, $commentAmount, $articles);
    populateSelectList($loq);

}

function deleteComment(&$loq, $id){
  $id = intval($id);
  $postid = $loq->_adb->GetOne('select postid from '.T_COMMENTS.' where commentid="'.$id.'"');
  $childcount = $loq->_adb->GetOne('select count(*) as c from '.T_COMMENTS .' where parentid="'.$id.'" group by commentid');
  if($childcount > 0) { // there are replies to the comment so we can't delete it.
    $loq->_adb->Execute('update '.T_COMMENTS.' set deleted="true", postername="", posteremail="", posterwebsite="", pubemail=0, pubwebsite=0, commenttext="Deleted Comment" where commentid="'.$id.'"');
  } else { // just delete the comment
    $loq->_adb->Execute('delete from '.T_COMMENTS.' where commentid="'.$id.'"');
  }
  $newnumcomments = $loq->_adb->GetOne('SELECT count(*) as c FROM '.T_COMMENTS.' WHERE postid="'.$postid.'" and deleted="false" group by postid');
  $loq->_adb->Execute('update '.T_POSTS.' set commentcount="'.$newnumcomments.'" where postid="'.$postid.'"');
  //$loq->modifiednow();
}

function editComment(&$loq, $commentid, $postid){
  $rval = true;
  if(!(is_numeric($commentid) && is_numeric($postid)))
    $rval = false;
  $comment = $loq->get_comment($postid,$commentid);
  if(!$comment)
    $rval = false;
  if($rval === true){
    $loq->assign('showeditform',TRUE);
    $loq->assign('comment',$comment[0]);
  }
  return $rval;
}

function saveEdit(&$loq){
  $rval = true;
  if(!(is_numeric($_POST['commentid'])))
    $rval = false;
  $title = stringHandler::clean($_POST['title']);
  $author = stringHandler::clean($_POST['author']);
  $email  = stringHandler::clean($_POST['email']);
  $websiteurl = stringHandler::clean($_POST['websiteurl']);
  $body = stringHandler::clean($_POST['body']);
  if($rval === true){
    $q = "update ".T_COMMENTS." set title='$title', postername='$author', posterwebsite='$websiteurl', posteremail='$email', commenttext='$body' where commentid='{$_POST['commentid']}'";
    if($loq->_adb->Execute($q) === true)
      $loq->assign('message', 'Comment <em>'.$title.'</em> saved');
  }
  return $rval;
}

/**
 * Retrieve select amount of comments from the system
 *
 * @param object $loq      A reference to a loq instance
 * @param int    $amount     The number of comments to retrieve
 * @param mixed  $posts      Which article to retrieve comments from; Setting to null retrieves from all articles
 */
function retrieveComments(&$loq, $amount, $article){
    if(is_null($article)){
        $filter = ' LIKE "%"';
    }
    else
        $filter = '='.$article;
    $sql = 'SELECT
        *
    FROM '.T_COMMENTS.'
    LEFT JOIN '.T_POSTS.'
        ON '.T_COMMENTS.'.postid = '.T_POSTS.'.postid
    WHERE deleted="false"
    AND '.T_COMMENTS.'.postid '.$filter.'
    ORDER BY '.T_COMMENTS.'.posttime DESC';
    $rs = $loq->_adb->GetAll($sql);
    $loq->assign('comments',$rs);
    $loq->assign('commentAmount', $amount);
    $loq->assign('commentPosts', $article);
}

function populateSelectList(&$loq){
    $posts_with_comments_q = "SELECT ".T_POSTS.".postid, ".T_POSTS.".title, count(*) c FROM ".T_COMMENTS.",  ".T_POSTS." 	WHERE ".T_POSTS.".postid = ".T_COMMENTS.".postid GROUP BY ".T_POSTS.".postid ORDER BY ".T_POSTS.".posttime DESC ";
    $rs = $loq->_adb->GetAll($posts_with_comments_q);
    $loq->assign('postselect', $rs);
}
?>