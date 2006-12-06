<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Comments
 * @author Kenneth Power <telcor@users.berlios.de>
 * @copyright &copy; 2006 Kenneth Power
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
 * A class to manage all comment related functions
 *
 * @version $Revision$
 */

class commentHandler {

    var $_post = null;
    /**
     * Constructor
     * Supplying a postid means we are working with a saved post
     */
    function commentHandler(&$db, $postid=null) {
        $postid = intval($postid);
        $this->_db = $db;
        $this->_highestlevel = 0;
        if(!is_null($postid) && $postid > 0){
            //$this->_db->debug = true;
            $this->_post = $postid;
            $sql = 'SELECT * from `'.T_COMMENTS.'` WHERE postid='.$this->_post;
            $rs = $this->_db->Execute($sql);
            if($rs){
                while($row = $rs->FetchRow()){
                    $this->_comments[$row[0]] = array(
                        'id'             => $row[0],
                        'parentid'       => $row[1],
                        'title'          => $row[3],
                        'type'           => $row[4],
                        'posttime'       => $row[5],
                        'postername'     => $row[6],
                        'posteremail'    => $row[7],
                        'posterwebsite'  => $row[8],
                        'posternotify'   => $row[9],
                        'pubemail'       => $row[10],
                        'pubwebsite'     => $row[11],
                        'ip'             => $row[12],
                        'commenttext'    => $row[13],
                        'delete'         => $row[14],
                        'onhold'         => $row[15]
                    );
                    $this->_thread[$row[1]][$row[0]] = $row[0];
                }
            }
        }
    }
    /**
     * Retrieve all comments for the specific post
     */
    function get_comments($fordisplay=false){
        if($fordisplay){
            if(is_array($this->_comments)){
                foreach($this->_comments as $id=>$comment){
                    $this->_comments[$id] = $this->prepFieldsForDisplay($comment);
                }
                $this->makethread(0, $this->_thread, 0);
                return $this->_comments;
            }
            else{
                return;
            }
        }
        else{
            return $this->_comments;
        }
    }
    function get_comment($cid=0){
        if($cid > 0){
            if(array_key_exists($cid, $this->_comments))
                return $this->_comments[$cid];
            else
                return;
        }
    }
    /**
    * Add a new comment to an article
    *
    * @param object $authImage AuthImage instance
    * @param object $post The post receiving the comment
    * @param int    $replyto The ID of the parent comment
    * @param array  $post_vars $_POST
    */
    function new_comment($post, $replyto, $post_vars) {
	$result = false;
        if($this->canProceed($post, $post_vars['imagecode'], $post_vars['comment'])){
            $vars = $this->prepFieldsForDB($post_vars, $post['postid'], $replyto);
            if ($post_vars['set_cookie']) {
                $this->setCommentCookie($vars['postername'], $vars['posteremail'], $vars['posterwebsite']);
            }

            $id = $this->saveComment($vars);
            if($id !== false && $id > 0){
                if(C_NOTIFY == 'true'){
                    $this->notify($vars['postername'], $post['permalink'],$vars['onhold'], $vars['commenttext']);
                }
                $result = $id;
            }
            else{
                $result = true;
                $this->_errors[] = array("Error", "Error inserting comment for post ".$post['title']);
                error_log(mysql_error(), 0);
            }
        }
        return $result;
    }

    /**
     * Prepare comment data for storage in the database
     *
     * Nothing peculiar to HTML display is done at this stage. Essentially the
     * comment is stored raw, for later manipulation for the display purposes.
     *
     * @param array $vars The comment data
     * @param int   $id The post id receiving this comment
     * @param int   $replyto If supplied, the id of the comment being replied to
     */
    function prepFieldsForDB($vars, $id, $replyto = 0){
        $rval['postername'] = stringHandler::clean($vars["name"]);
        if (empty($rval['postername']))
            $rval['postername'] = "Anonymous";
        $rval['posteremail'] = stringHandler::clean($vars["email"]);
        $rval['title'] = stringHandler::clean($vars["title"]);
        $rval['posterwebsite'] = stringHandler::clean($vars["website"]);
        $rval['commenttext'] = $this->processCommentText($vars["comment"]);
        $rval['pubemail'] = ($vars["public_email"] == 1) ? 1 : 0;
        $rval['pubwebsite'] = ($vars["public_website"] == 1) ? 1 : 0;
        $rval['posternotify'] = ($vars["notify"] == 1) ? 1 : 0;
        $rval['posttime'] = strtotime(gmdate("M d Y H:i:s"));
        $rval['ip'] = $_SERVER['REMOTE_ADDR'];
        $rval['onhold'] = ($this->needsModeration($rval['commenttext'])) ? 1 : 0;
        $rval['postid'] = $id;
        if ($replyto > 0)
            $rval['parentid'] = $replyto;
        $rval['type'] = 'comment';
        return $rval;
    }


    /**
    * Save the comment/trackback
    *
    * The SQL statement for saving data is built based upon the values of
    * `$vars`. It is an associative array where the keys are the `T_CONFIG`
    * field names and the elements are values for the fields. On success, the
    * row id(integer) is returned, on failure either false (boolean) or
    * an error message (string) is returned.
    *
    * @param array $vars An associative array
    * @return mixed
    */
    function saveComment($vars){
        $rval = false;
        if(is_array($vars)){
            if($this->_db->AutoExecute(T_COMMENTS, $vars, 'INSERT')){
                $rval = $this->_db->Insert_Id();
            }
        }
        return $rval;
    }

    /**
    * Tests comment text against moderation criteria
    *
    * @param string $comment The comment text
    * @return bool
    */
    function needsModeration($comment){
        $rval = false;
        $comment = strtolower($comment);
        if (C_COMMENT_MODERATION == 'all') {
            $rval = true;
        }
        elseif (C_COMMENT_MODERATION == 'urlonly') {
            if(strpos($comment, '<a') !== false)
                $rval = true;
        }
        return $rval;
    }

    /**
    * Initiates a variety of tests
    *
    * An array is returned with the following fields
    * and values:
    * +=============================================+
    * | proceed    |  true if all passed all tests  |
    * |            |  false if failed any test      |
    * +============+================================+
    * | message    | An array of error messages:    |
    * |            | array(message_title,           |
    * |            |   message_text);               |
    * +============+================================+
    *
    * @param object $post The article receiving the comment
    * @param object $authImage AuthImage instance
    * @param string $code Captcha code as typed by the user
    * @param string $comment Comment text
    * @return array
    */
    function canProceed(&$post, $code, $comment){
        $rval = true;
        if($this->isFlooding( $_SERVER['REMOTE_ADDR'], time())){
            $rval = false;
            $this->_errors[] = array("Comment Flood Protection" => "Error adding comment. You have tried to make a comment too soon after your last one. Please try again later. This is a Loquacity spam prevention measure");
        }
        if($this->isDisabled($post)){
            $rval = false;
            $this->_errors[] = array("Error adding comment" => "Comments have been turned off for this post");
        }
        if($this->failsCaptcha($code)){
            $rval = false;
            $this->_errors[] = array('Error adding comment' => 'Supplied text does not match what was on the image');
        }
        return $rval;
    }

    /**
    * Checks whether commenting is disabled for this post
    *
    * @param object $post
    * @return bool
    */
    function isDisabled($post){
        $rval = false;
        if ($post['allowcomments'] == 'disallow' or ($post['allowcomments'] == 'timed' and $post['autodisabledate'] < time()))
            $rval = true;
        return $rval;
    }

    /**
    * Performs various transformations on text. Hyperlinks have
    * the redirector added and are wrapped in A tags (if not already wrapped).
    * Special characters are transformed into HTML entities.
    *
    * @param string $comment Comment text
    * @return string
    */
    function processCommentText($comment){
        //Policy: only a, b, i, strong, code, acrynom, blockquote, abbr are allowed
        $comment = stringHandler::removeTags($comment, '<a><b><i><strong><code><acronym><blockquote><abbr>');
        /*if(stringHandler::containsLinks($comment)){
            $comment = stringHandler::transformLinks($comment);
        }*/
        //Policy: translate HTML special characters to their HTML entities
        $comment = $this->encodeHTML($comment);
        //Policy: line breaks converted automatically
        return nl2br($comment);
    }

    /**
    * Checks whether an attempt at comment flooding is being made
    *
    * @param string $ip IP Address of commentor
    * @param int $now Unix Timestamp of current time
    */
    function isFlooding($ip, $now){
        $rval = false;
        if (C_COMMENT_TIME_LIMIT > 0) {
            $fromtime = $now - (C_COMMENT_TIME_LIMIT * 60);
            $rs = $this->_db->Execute("select * from ".T_COMMENTS." where ip='".$ip."' and posttime > ".$fromtime);
            if ($rs !== false && $rs->RecordCount() > 0) {
                $rval = true;
            }
        }
        return $rval;
    }

    /**
    * Saves comment details in a cookie
    *
    * @param string $name Commentors name
    * @param string $email Commentors email address
    * @param string $website Commentors website
    * @return void
    */
    function setCommentCookie($name, $email, $website){
        $ctime = time()+3600*24*30;
        setcookie("postername", $name, $ctime);
        setcookie("posteremail", $email, $ctime);
        setcookie("posterwebsite", $website, $ctime);
        $value = base64_encode(serialize(array ('web' => $website, 'mail' => $email, 'name' => $name)));
        setcookie("bBcomment", $value, time() + (86400 * 360));
    }

    /**
    * Tests what user typed against the captcha
    *
    * @param string $code Captcha code typed by user
    * @return bool
    */
    function failsCaptcha($code){
        $rval = true;
        if(C_IMAGE_VERIFICATION == 'true' && !empty($code)) { //Some templates may not have the iamge verification enabled
            if (!PhpCaptcha::Validate($code)){
                $rval = false;
            }
        }
        return $rval;
    }

    /**
    * Notifies blog author of new comment
    *
    * @param string $name Commentors name
    * @param string $link Link to comment entry
    * @param int    $onhold Whether or not comment requires moderation
    * @param string $comment Text of the comment
    * @return void
    */
    function notify($name, $link, $onhold, $comment){
        include_once (LOQ_APP_ROOT."includes/mail.php");
        $message = $name." has posted a comment in reply to your blog entry at ".$link."\n\nComment: ".$comment."\n\n";
        if ($onhold == 1)
            $message .= "You have selected comment moderation and this comment will not appear until you approve it, so please visit your blog and log in to approve or reject any comments\n";
        notify_owner("New comment on your blog", $message);
    }

    /**
     * Updates the number of comments for a post in the post table
     *
     * @deprecated This will go away soon. It can easily be obtained when by a query, removing this step
     * @param int $postid
     */
    function updateCommentCount($postid){
        $rs = $this->_db->Execute("SELECT count(*) as c FROM ".T_COMMENTS." WHERE postid='$postid' and deleted='false' group by postid");
        if($rs !== false){
            $newnumcomments = $rs->fields[0];
            $this->_db->Execute("update ".T_POSTS." set commentcount='$newnumcomments' where postid='$postid'");
        }
    }

    /**
     * Enforces HTML Encoding policy on comment text
     *
     * Policy states HTML special characters (&, ", etc) be translated to
     * their HTML entity equivalents for HTML display purposes. In doing this,
     * we must maintain the HTML tags (a, b, i, strong, code, acrynom, blockquote,
     * abbr) policy allows.
     */
    function encodeHTML($comment){
        //Make certain we don't encode the allowed tags
        //Policy: only a, b, i, strong, code, acrynom, blockquote, abbr are allowed

        $comment = str_replace("\r\n", '%%%COMMENT:TRANSFORM:NEWLINE:%%% ', $comment);
        $comment = str_replace("\n", '%%%COMMENT:TRANSFORM:NEWLINE:%%% ', $comment);
        $_blocks = array(
            'a' => array('pattern'=>'/<a[^>]{0,}>.*?<\/a>/is'),
            'abbr' => array('pattern'=>'/<abbr[^>]{0,}>.*?<\/abbr>/is'),
            'b' => array('pattern'=>'/<b>.*?<\/b>/is'),
            'i' => array('pattern'=>'/<i>.*?<\/i>/is'),
            'strong' => array('pattern'=>'/<strong>.*?<\/strong>/is'),
            'em' => array('pattern' => '/<em>.*?<\/em>/is'),
            'code' => array('pattern' => '/<code[^>]{0,}>.*?<\/code>/is'),
            'acronym' => array('pattern' => '/<acronym[^>]{0,}>.*?<\/acronym>/is'),
            'blockquote' => array('pattern' => '/<blockquote[^>]{0,}>.*?<\/blockquote>/is')
            );
        foreach($_blocks as $tag=>$arr){
            $match = array();
            preg_match_all($arr['pattern'], $comment, $match);
            $_blocks[$tag]['match'] = $match;
            $replace = '%%%COMMENT:TRANSFORM:'.strtoupper($tag).'%%% ';
            $comment = preg_replace($arr['pattern'], $replace, $comment);
        }
        $comment = htmlspecialchars($comment);
        foreach($_blocks as $tag=>$arr){
            $search_str= '%%%COMMENT:TRANSFORM:'.strtoupper($tag).'%%%';
            $_len = strlen($search_str);
            $_pos = 0;
            for ($_i=0, $_count=count($arr['match']); $_i<$_count; $_i++)
                if (($_pos=strpos($comment, $search_str, $_pos))!==false)
                    $comment = substr_replace($comment, $arr['match'][$_i], $_pos, $_len);
                else
                    break;
        }
        //$comment = str_replace('%%%COMMENT:TRANSFORM:NEWLINE:%%%', "\r\n", $comment);
        $comment = str_replace('%%%COMMENT:TRANSFORM:NEWLINE:%%%', "\n", $comment);
        return $comment;
    }

    function prepFieldsForDisplay($vars, $replyto=0){
        $rval['id'] = $vars['id'];
        $rval['postername'] = htmlspecialchars($vars["postername"]);
        if (empty($rval['postername']))
            $rval['postername'] = "Anonymous";
        $rval['posteremail'] = htmlspecialchars(stripslashes($vars["posteremail"]));
        $rval['title'] = htmlspecialchars($vars["title"]);
        $rval['posterwebsite'] = stringHandler::transformLinks(htmlspecialchars(stripslashes($vars["posterwebsite"])));
        $rval['commenttext'] = $this->processCommentText(stripslashes($vars["commenttext"]));
        $rval['pubemail'] = ($vars["pubemail"] == 1) ? true : false;
        $rval['pubwebsite'] = ($vars["pubwebsite"] == 1) ? true : false;
        $rval['posternotify'] = ($vars["posternotify"] == 1) ? true : false;
        $rval['posttime'] = $vars['posttime'];
        $rval['ip'] = $vars['ip'];
        $rval['onhold'] = ($this->needsModeration($rval['commenttext'])) ? true : false;
        $rval['postid'] = $this->_post;
        $rval['parent'] = ($vars['parentid'] > 0) ? $vars['parentid'] : false;
        $rval['type'] = $vars['type'];
        $rval['deleted'] = ($vars['deleted'] == 1) ? true : false;
        $rval['link'] = LOQ_APP_URL.'trackback.php/'.$this->_post.'/'.$vars['id'];

        return $rval;
    }
    ////
    // !changes the array type and sets some default values for each comment
    function format_comment ($comment) {
        if($comment['data']['pubemail'] > 0) {
            $commentr['email'] = $comment['data']['posteremail'];
        }

        if($comment['data']['pubwebsite'] > 0) {
            $commentr['website'] = $comment['data']->posterwebsite;
        }
        if($comment['data']['pubemail'] > 0 && $comment['data']['posteremail'] != '') {
            $commentr['emaillink'] = "<a href='mailto:".$comment['data']['posteremail']."'>@</a>";
        } else $commentr['emaillink'] = '';


        if($comment['data']['pubwebsite'] > 0 && $comment['data']['posterwebsite'] != '') {
            $commentr['websitelink'] = $comment['data']['posterwebsite'];
        } else $commentr['websitelink'] = '';

        $commentr['websiteurl'] = $comment['data']['posterwebsite'];
        $commentr['permalink'] = "<a name='comment{$comment['data']['commentid']}'></a>
                          <a href='".$this->_get_comment_permalink($postid,$comment['data']['commentid'])."'>#</a>";

        $commentr['permalinkurl'] = $this->_get_comment_permalink($postid,$comment['data']['commentid']);

        $commentr['replylinkurl'] = $this->_get_entry_permalink($postid);

        if(substr_count($commentr['replylinkurl'],"?") == 1) {
                $commentr['replylinkurl'] .= "&amp;";
        } else {
            $commentr['replylinkurl'] .= "?";
        }

        $commentr['replylinkurl'] .= "replyto={$comment['data']['commentid']}#commentform";

        $commentr['replylink'] = "<a href='".$commentr['replylinkurl']."'>Reply</a>";

        $commentr['commentid'] = $comment['data']['commentid'];
        $commentr['postid'] = $postid;

        if($comment['level'] > 0 ) {
            $commentr['level25'] = $comment['level']*25;
        } else {
            $commentr['level25'] = 1;
        }
        if($comment['level'] > 0 ) {
            $commentr['level15'] = $comment['level']*15;
        } else {
            $commentr['level25'] = 1;
        }
        if($comment['level'] > 0 ) {
            $commentr['level10'] = $comment['level']*10;
        } else {
            $commentr['level10'] = 1;
        }

        $commentr['level'] = $comment['level'];

        if($this->highestlevel == 0 || $comment['level'] == 0) {
            $commentr['levelpercent']   = 0;
            $commentr['levelhalfpercent']   = 0;
        } else {
            $commentr['levelpercent']   = floor(( 100 / $this->highestlevel )*$comment['level']);
            $commentr['levelhalfpercent']   = floor(( 50 / $this->highestlevel )* $comment['level']);
        }

        $commentr['levelpercentremainder'] = 100 - $commentr['levelpercent'];

        $commentr['trackbackurl']  = $this->_get_comment_trackback_url($postid,$comment['data']->commentid);

        return $commentr;

    }

    function makethread($index,$table,$level){
        if($level > $this->_highestlevel){
            $this->_highestlevel = $level;
        }
        $list=$table[$index];
        while(list($key,$val)=each($list)){
            $this->_comments[$key]['level'] = $level;
            if($level > 0 ) {
                $this->_comments[$key]['level25'] = $level*25;
            } else {
                $this->_comments[$key]['level25'] = 1;
            }
            if($level > 0 ) {
                $this->_comments[$key]['level15'] = $level*15;
            } else {
                $this->_comments[$key]['level25'] = 1;
            }
            if($level > 0 ) {
                $this->_comments[$key]['level10'] = $level*10;
            } else {
                $this->_comments[$key]['level10'] = 1;
            }
            if($this->_highestlevel == 0 || $this->_comments['level'] == 0) {
                $this->_comments[$key]['levelpercent']   = 0;
                $this->_comments[$key]['levelhalfpercent']   = 0;
            } else {
                $this->_comments[$key]['levelpercent'] = floor(( 100 / $this->_highestlevel )*$this->_comments[$key]['level']);
                $this->_comments[$key]['levelhalfpercent'] = floor(( 50 / $this->_highestlevel )* $this->_comments[$key]['level']);
            }

            $this->_comments[$key]['levelpercentremainder'] = 100 - $this->_comments[$key]['levelpercent'];
            if ((isset($table[$key]))){
                $this->makethread($key,$table,$level+1);
            }
        }
        return true;
    } // end function makethread
    function debug($msg){
        echo '<pre>';
        var_dump($msg);
        echo '</pre>';
    }
}
?>