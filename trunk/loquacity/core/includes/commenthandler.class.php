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
    function commentHandler(&$db) {
        $this->_db = $db;
    }
    
    /**
     * Retrieve all comments for a specific post
     * 
     * Returns a dataset of comments for the post matching $postid. If special processing is requested, the resulting
     * dataset is an index-based array, otherwise it's a an ADODB::Recordset object.
     * 
     * @param int $postid
     * @param string $process Whether to process the resulting data for special purposes. Accepts one of the following:
     *	<table>
     * 		<tr>
     * 			<td><strong>none</strong></td>
     * 			<td>Do no further processing</td>
     * 		</tr>
     *		<tr>
     * 			<td>html</td>
     * 			<td>Apply rules to make data safe for HTML presentation</td>
     * 		</tr>
     * 	</table>
     * @param string $format Apply specified formatting to comments. Only applied when $pocess does not equal 'none'. Accepts one of the following:
     *	<table>
     * 		<tr>
     * 			<td><strong>none</strong></td>
     * 			<td>Do not applying any formating</td>
     * 		</tr>
     * 		<tr>
     * 			<td>flat</td>
     * 			<td>The same as none</td>
     * 		</tr>
     * 		<tr>
     * 			<td>thread</td>
     * 			<td>Created nested comment threads</td>
     * 		</tr>
     * 	</table>
     * @return mixed
     */
    function getComments($postid=null, $process='none', $format='none'){
    	if(is_null($postid)){
    		return false;
    	}
    	$format = strtolower($format);
    	$process = strtolower($process);
    	if($process === 'html' && ($format === 'flat' || $format === 'none')){
    		return $this->flatComments($postid);
    	}
    	else if($process === 'html' && $format === 'thread'){
    		return $this->threadedComments($postid);
    	}
    	else{
    		return $comments; 
    	}
    }
    /**
     * Retrieve a single comment by id
     * 
     * @param int $cid Id of the comment to retrieve
     * @param string $process Apply special processing rules to resulting dataset. Accepts one of the following:
     * <table>
     * 		<tr>
     * 			<td><strong>none</strong></td>
     * 			<td>Do no further processing</td>
     * 		</tr>
     *		<tr>
     * 			<td>html</td>
     * 			<td>Apply rules to make data safe for HTML presentation</td>
     * 		</tr>
     * 	</table>
     * @return array
     */
    function getComment($cid=0, $process='none'){
    	$cid = intval($cid);
        if($cid > 0){
        	$stmt = $this->_db->Prepare('SELECT comm.* FROM `'.T_COMMENTS.'` AS comm WHERE `commentid`=?');
        	$comment = $this->_db->Execute($stmt, array($cid));
        	if($process !== 'none'){
        		return array($this->processForHTML($comment));
        	}
        	else{
        		return $comment[0];
        	}
        }
    }
    
    /**
    * Process the textual elements of the comment, making fit for HTML display. This removes Javascript and other dangerous text
    * for safety reasons.
    *
    * @param object ADODB_Recordset
    * @return array
    */
	function processForHTML(&$comment){
		return array(
					'id' => $comment->fields[0],
					'title' => htmlspecialchars(stringHandler::removeJs(stringHandler::clean($comment->fields[3]))),
					'body' => $this->processCommentText(stringHandler::removeJs($comment->fields[13])),
					'author' => stringHandler::removeJs($comment->fields['postername']),
					'email' => stringHandler::removeJs($comment->fields[7]),
					'website' => stringHandler::transformLinks(stringHandler::removeJs($comment->fields[8])),
					'posted' => $comment->fields[5],
					'url' => (defined('CLEANURLS')) ?  BLOGURL.'trackback/'.$comment->fields[2].'/'.$comment->fields[0] : BLOGURL.'trackback.php&amp;tbpost='.$comment->fields[2].'&amp;cid='.$comment->fields[0],
					'type' => $comment->fields[4],
					'onhold' => ($comment->fields[15] == 1) ? true : false,
					'reply' => $postlink.'?replyto='.$comment->fields[0].'#commentform',
                    'parent' => $comment->fields['parentid'],
					);
	}
	/**
	* Retrieves comments for a particular post and creates a threaded listing.
	*
    * @param int $postid
    * @return array
	*/
    function threadedComments($postid){
		$stmt = $this->_db->Prepare('SELECT * FROM `'.T_COMMENTS.'` WHERE postid=? ORDER BY commentid ASC');
   		$comments = $this->_db->Execute($stmt, array($postid));
    	if(is_object($comments) && !$comments->EOF){
			$tc = array();
			$postlink = (defined('CLEANURLS')) ? BLOGURL.'item/'.$postid.'/' : BLOGURL.'?postid='.$postid;
    		while(!$comments->EOF){
				$tc[$comments->fields['parentid']][$comments->fields['commentid']] = $this->processForHTML(&$comments);
    			$comments->MoveNext();
    		}
            $this->_createThreading(0, $tc, 0);
			return $this->_threaded;
    	}
    }
    
    /**
    * Transforms list of comments into a thread suitable for display purposes. Recursive.
    * Expects the $comments array to be in a particular format:
    *   array[ parentid ][ commentid] = comment data 
    *
    * @param int $parent The current Parent ID being processed
    * @param array $comments A 2-dimension array of comments
    * @param int $level The current nested level
    */
    function _createThreading($parent, $comments, $level){
        while(list($cid, $data) = each($comments[$parent])){
            $data['level'] = $level * 25;
            $this->_threaded[] = $data;
            if(isset($comments[$cid])){
                $this->_createThreading($cid, $comments, $level+1);
            }
        }
    }
    
    /**
    * Retrieves comments for a particular post and creates a flat-list for display
    *
    * @param int $postid
    * @return array
    */
    function flatComments($postid){
        $stmt = $this->_db->Prepare('SELECT * FROM `'.T_COMMENTS.'` WHERE postid=? ORDER BY commentid ASC');
   		$comments = $this->_db->Execute($stmt, array($postid));
    	if(is_object($comments) && !$comments->EOF){
    		while(!$comments->EOF){
                $c = $this->processForHTML(&$comments);
                $c['level'] = 0;
                $this->_flat[] = 
    			$comments->MoveNext();
    		}
            return $this->_flat;
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
		if( $this->canProceed($post, $post_vars['imagecode'], $post_vars['comment']) ){
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
        if (empty($rval['postername'])){
            $rval['postername'] = "Anonymous";
		}
        $rval['posteremail'] = $this->_db->qstr(stringHandler::clean($vars["email"]), get_magic_quotes_gpc());
        $rval['title'] = (strlen($vars["title"]) > 0) ? $vars['title'] : 'Title';
        $rval['posterwebsite'] = stringHandler::transformLinks(stringHandler::clean($vars["website"]));
        $rval['commenttext'] = $this->processCommentText($vars["comment"]);
        $rval['pubemail'] = ($vars["public_email"] == 1) ? 1 : 0;
        $rval['pubwebsite'] = ($vars["public_website"] == 1) ? 1 : 0;
        $rval['posternotify'] = ($vars["notify"] == 1) ? 1 : 0;
        $rval['posttime'] = strtotime(gmdate("M d Y H:i:s"));
        $rval['ip'] = $_SERVER['REMOTE_ADDR'];
        $rval['onhold'] = ($this->needsModeration($rval['commenttext'])) ? 1 : 0;
        $rval['postid'] = $id;
        $rval['parentid'] = $replyto;
        $rval['type'] = 'comment';
        return $rval;
    }


    /**
    * Save the comment/trackback
    *
    * The SQL statement for saving data is built based upon the values of
    * `$vars`. It is an associative array where the keys are the `T_COMMENT`
    * field names and the elements are values for the fields. On success, the
    * row id(integer) is returned, on failure either false (boolean) or
    * an error message (string) is returned.
    *
    * @param array $vars
    * @return mixed
    */
    function saveComment($vars){
        //$this->_db->debug = true;
        $rval = false;
        if(is_array($vars)){
            $stmt = $this->_db->Prepare('INSERT INTO `'.T_COMMENTS.'` (parentid, postid, title, type, posttime, postername, posteremail, posterwebsite, posternotify, pubemail, pubwebsite, ip, commenttext, onhold) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?) ');
            if($this->_db->Execute($stmt, array($vars['parentid'], $vars['postid'], $vars['title'], $vars['type'], $vars['posttime'], $vars['postername'], $vars['posteremail'], $vars['posterwebsite'], $vars['posternotify'], $vars['pubemail'], $vars['pubwebsite'], $vars['ip'], $vars['commenttext'], $vars['onhold'])) !== false){
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
    function canProceed($post, $code, $comment){
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
    * @return bool False if commetns are allowed, True if disabled
    */
    function isDisabled($post){
        $rval = false;
        if ($post['allowcomments'] == 'disallow' || ($post['allowcomments'] == 'timed' && $post['autodisabledate'] < time())){
            $rval = true;
        }
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
    * @return bool True if violates timespan rule, False if doesn't
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
        return false;
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
    function debug($msg){
        echo '<pre>';
        var_dump($msg);
        echo '</pre>';
    }
	
	function errors( $mode='text' ){
		$message = '';
		switch( $mode ){
			case 'text':
				foreach( $this->_errors as $err ){
					if( is_array( $err ) ){
							foreach( $err as $key=>$ind ){
								$message .= '['.$key.'] '.$ind."\n";
							}
					}
					else{
						$message .= $err."\n";
					}
				}
				break;
			case 'html':
				foreach( $this->_errors as $err ){
					if( is_array( $err ) ){
						foreach( $err as $key=>$ind ){
							$message .= "<div class=\"error\">".htmlspecialchars( $key )."</div><div class=\"error_message\">".htmlspecialchars( $ind )."</div>\n";
						}
					}
					else{
						$message .= "<div class=\"error\">".htmlspecialchars( $err )."</div>\n";
					}
				}
				break;
			default:
				break;
		}
		return $message;
	}
}
?>