<?php
// admin.comments.php - administer comments
/*
** bBlog Weblog http://www.bblog.com/
** Copyright (C) 2003  Eaden McKee <email@eadz.co.nz>
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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

function admin_plugin_comments_run(&$bBlog) {
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
                    deleteComment($bBlog, $val);
                }
            }
            break;
        case "Edit" :
            $commentid = intval($_GET['editComment']);
            $postid = intval($_GET['postid']);
            editComment($bBlog, $commentid, $postid);
            break;
        case "editsave" :
          saveEdit($bBlog);
          break;
        case "Approve":
            if(is_array($_POST['commentid'])){
                foreach($_POST['commentid'] as $key=>$val)
                    $bBlog->_adb->Execute("UPDATE ".T_COMMENTS." SET onhold='0' WHERE commentid='".intval($val)."'");
            }
            break;
        case "filter":
        default : // show form
          break;
    }

    retrieveComments($bBlog, $commentAmount, $articles);
    populateSelectList($bBlog);

}

function deleteComment(&$bBlog, $id){
  $id = intval($id);
  $postid = $bBlog->get_var('select postid from '.T_COMMENTS.' where commentid="'.$id.'"');
  $childcount = $bBlog->get_var('select count(*) as c from '.T_COMMENTS .' where parentid="'.$id.'" group by commentid');
  if($childcount > 0) { // there are replies to the comment so we can't delete it.
    $bBlog->_adb->Execute('update '.T_COMMENTS.' set deleted="true", postername="", posteremail="", posterwebsite="", pubemail=0, pubwebsite=0, commenttext="Deleted Comment" where commentid="'.$id.'"');
  } else { // just delete the comment
    $bBlog->_adb->Execute('delete from '.T_COMMENTS.' where commentid="'.$id.'"');
  }
  $newnumcomments = $bBlog->get_var('SELECT count(*) as c FROM '.T_COMMENTS.' WHERE postid="'.$postid.'" and deleted="false" group by postid');
  $bBlog->_adb->Execute('update '.T_POSTS.' set commentcount="'.$newnumcomments.'" where postid="'.$postid.'"');
  $bBlog->modifiednow();
}

function editComment(&$bBlog, $commentid, $postid){
  $rval = true;
  if(!(is_numeric($commentid) && is_numeric($postid)))
    $rval = false;
  $comment = $bBlog->get_comment($postid,$commentid);
  if(!$comment)
    $rval = false;
  if($rval === true){
    $bBlog->assign('showeditform',TRUE);
    $bBlog->assign('comment',$comment[0]);
  }
  return $rval;
}

function saveEdit(&$bBlog){
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
    if($bBlog->_adb->Execute($q) === true)
      $bBlog->assign('message', 'Comment <em>'.$title.'</em> saved');
  }
  return $rval;
}

/**
 * Retrieve select amount of comments from the system
 *
 * @param object $bBlog      A reference to a bBlog instance
 * @param int    $amount     The number of comments to retrieve
 * @param mixed  $posts      Which article to retrieve comments from; Setting to null retrieves from all articles
 */
function retrieveComments(&$bBlog, $amount, $article){
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
    $rs = $bBlog->_adb->GetAll($sql);
    $bBlog->assign('comments',$rs);
    $bBlog->assign('commentAmount', $amount);
    $bBlog->assign('commentPosts', $article);
}

function populateSelectList(&$bBlog){
    $posts_with_comments_q = "SELECT ".T_POSTS.".postid, ".T_POSTS.".title, count(*) c FROM ".T_COMMENTS.",  ".T_POSTS." 	WHERE ".T_POSTS.".postid = ".T_COMMENTS.".postid GROUP BY ".T_POSTS.".postid ORDER BY ".T_POSTS.".posttime DESC ";
    $rs = $bBlog->_adb->GetAll($posts_with_comments_q);
    $bBlog->assign('postselect', $rs);
}
?>