<?php

/**
 * File: comments.class.php
 * Author: Kenneth Power <kenneth.power@gmail.com>
 * Date: May 4, 2006
 * 
 * A class to manage all comment related functions
 */
class comments {

    var $_post = null;
    /**
     * Constructor
     * Supplying a postid means we are working with a saved post
     */
    function comments(&$db, $postid=null) {
        $this->_db = $db;
        if(!isnull($postid)){
            $this->_post = $postid;
            $sql = 'SELECT * from `'.T_COMMENTS.'` WHERE postid='.$this->_post;
        }
    }
    /**
     * Retrieve all comments for the specific post
     */
    function get_comments($postid=null){
        if(!is_null($postid)){
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
    function new_comment($postid,$replyto = 0) {

        $post = $this->_ph->get_post($postid,FALSE,TRUE);
        if(!$post){
            // this needs to be fixed...
             $this->standalone_message("Error adding comment","couldn't find post id $postid");
        }
        elseif($post->allowcomments == ('disallow') or ($post->allowcomments == 'timed' and $post->autodisabledate < time() )){
            $this->standalone_message("Error adding comment","Comments have been turned off for this post");
        }
        else {
            $postername = my_addslashes(htmlspecialchars($_POST["name"]));
            if($postername == '')
                 $postername = "Anonymous";
            $posteremail = my_addslashes(htmlspecialchars($_POST["email"]));
            $title = my_addslashes(htmlspecialchars($_POST["title"]));
            $posterwebsite = my_addslashes(htmlspecialchars($_POST["website"]));
            if((substr(strtolower($posterwebsite),0,7) != 'http://') && $posterwebsite !='') {
                $posterwebsite = 'http://'.$posterwebsite;
            }
            $comment = my_addslashes($_POST["comment"]);
            if($_POST["public_email"] == 1) $pubemail = 1; else $pubemail = 0;
            if($_POST["public_website"] == 1) $pubwebsite = 1; else $pubwebsite = 0;
            if($_POST["notify"] == 1) $notify = 1; else $notify = 0;
            $now = time();
            $remaddr = $_SERVER['REMOTE_ADDR'];
    
            if ($_POST['set_cookie']) {
                $value = base64_encode(serialize(array('web' => $posterwebsite, 'mail' => $posteremail, 'name' => $postername)));
                setcookie ("bBcomment", $value, time() + (86400 * 360));
            }
    
            $moderated = FALSE;
            $onhold = '0';
            if(C_COMMENT_MODERATION == 'all') {
                $moderated = TRUE;
            } elseif (C_COMMENT_MODERATION == 'urlonly') {
                if($comment != preg_replace('!<[^>]*?>!', ' ', $comment)) {
                    // found html tags
                    $moderated = TRUE;
                }
                if($comment != preg_replace("#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="\2://\3" target="_blank">\2://\3</a>', $comment)) {
                    $moderated = TRUE;
                }
                if($comment != preg_replace("#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="http://\2.\3" target="_blank">\2.\3</a>', $comment)) {
                    $moderated = TRUE;
                }
            }
        
            if($moderated == TRUE) $onhold='1';
            
            if(C_COMMENT_TIME_LIMIT >0) {
                $fromtime = $now - (C_COMMENT_TIME_LIMIT * 60);
                $this->query("select * from ".T_COMMENTS." where ip='$remaddr' and posttime > $fromtime");
                if($this->num_rows >0) {
                    $this->standalone_message("Comment Flood Protection", "Error adding comment. You have tried to make a comment too soon after your last one. Please try again later. This is a bBlog spam prevention mesaure");
                    
                }
        
            }
        
            if($replyto > 0 && is_numeric($replyto)) $parentidq = " parentid='$replyto', ";
            $q = "insert into ".T_COMMENTS."
                    set $parentidq
                    postid='$postid',
                    title='$title',
                    posttime='$now',
                    postername='$postername',
                    posteremail='$posteremail',
                    posterwebsite='$posterwebsite',
                    posternotify='$notify',
                    pubemail='$pubemail',
                    pubwebsite='$pubwebsite',
                    ip='$remaddr',
                    commenttext='$comment',
                    onhold='$onhold',
                    type='comment'";
            $this->query($q);
            $insid = $this->insert_id;
            if($insid < 1) {
                $this->standalone_message("Error", "Error inserting comment : ".mysql_error());
            } else {
                // notify
                include_once(BBLOGROOT."inc/mail.php");
                $message = htmlspecialchars($postername)." has posted a comment in reply to your blog entry at ".$this->_get_entry_permalink($postid)."\n";
                if($onhold == 1) $message .= "You have selected comment moderation and this comment will not appear until you approve it, so please visit your blog and log in to approve or reject any comments\n";
                notify_owner("New comment on your blog",$message);
        
                    $newnumcomments = $this->get_var("SELECT count(*) as c FROM ".T_COMMENTS." WHERE postid='$postid' and deleted='false' group by postid");
                $this->query("update ".T_POSTS." set commentcount='$newnumcomments' where postid='$postid'");
                    $this->modifiednow();
        
                // This is used when an alternate location is desired as the result of a successful post.
                if(isset($_POST['return_url'])) {
                    $ru = str_replace('%commentid%',$insid,$_POST['return_url']);
                    header("Location: ".$ru);
                } else {
                    header("Location: ".$this->_get_entry_permalink($postid)."#comment".$insid);
                }
                    ob_end_clean(); // or here.. hmm.
                    exit;
            }
        }
    
    } // end function new_comment
    function get_comments ($postid,$replyto=FALSE) {
        $this->com_order_array = array();
        if(is_numeric($replyto)) {
            $commentidq = " AND commentid='$replyto' ";
        }
        
        $commentids = $this->get_results("select *
        FROM ".T_COMMENTS."
        where postid='$postid'
        $commentidq
        order by commentid");
                                             
        if($this->num_rows > 0) { // there are coments!
            foreach($commentids as $row ) {
                $table[$row->parentid][$row->commentid] = $row->commentid;
            }
    
            // get the actual comments
            //$comments=$bBlog->get_results("SELECT * FROM ".T_COMMENTS." WHERE postid='$postid' $commentidq ");
        
            // make an array of comments, with the commentid as the key - there must be a better way!
            foreach($commentids as $comment) {
                $this->com_finalar[$comment->commentid] = $comment;
            }
            // populate $this->com_order_array with the comments in order!
            $this->makethread(0,$table,0);
            
            $commentsfinalarray = array();
            // the function that displays comments!
            foreach($this->com_order_array as $comment) {
                $commentsfinalarray[] = $this->format_comment($comment);
            }
        }
        $this->assign("commentreplytitle","Re: ".$this->get_var("select title from ".T_POSTS." where postid='$postid'"));
        return $commentsfinalarray;
    }
    // due to some weird bug with the recursive function,
    // there is a bit of duplicated code here for the meantime
    
    function get_comment ($postid,$replyto=FALSE,$raw = FALSE) {
        if(is_numeric($replyto)) $commentidq = " AND commentid='$replyto' ";
        $comment['data'] = $this->get_row("select *
                                             FROM ".T_COMMENTS."
                                             where postid='$postid'
                                             $commentidq
                                             order by commentid");
    
        if($this->num_rows != 1) return FALSE;
        if($raw) return $comment['data'];
        $comment['level'] = 0; // not displaying one comment in a thread
        $commentsfinalarray[] = $this->format_comment($comment);
        if($replyto) {
            if(substr($commentsfinalarray[0]['title'],0,3) == 'Re:') {
                $this->assign("commentreplytitle",$commentsfinalarray[0]['title']);
            } else {
                $this->assign("commentreplytitle","Re: ".$commentsfinalarray[0]['title']);
            }
        }
        return $commentsfinalarray;
    }
    ////
    // !changes the array type and sets some default values for each comment
    function format_comment ($comment) {
        $postid = $comment['data']->postid;
        if($comment['data']->deleted == "true") {
            $commentr['deleted'] = TRUE;
        }
        
        $commentr['body']     = $comment['data']->commenttext;
        $commentr['posttime'] = $comment['data']->posttime;
            $commentr['posted'] = $comment['data']->posttime;
        
        $commentr['name'] = $comment['data']->postername;
        $commentr['author'] = $comment['data']->postername;
        $commentr['title'] = $comment['data']->title;
        $commentr['type'] = $comment['data']->type;
    
        if($comment['data']->onhold == 1) $commentr['onhold'] =TRUE;
    
            if($comment['data']->pubemail > 0) {
            $commentr['email'] = $comment['data']->posteremail;
        }
                    
        if($comment['data']->pubwebsite > 0) {
            $commentr['website'] = $comment['data']->posterwebsite;
        }
    
    
    
        if($comment['data']->pubemail > 0 && $comment['data']->posteremail != '') {
            $commentr['emaillink'] = "<a href='mailto:".$comment['data']->posteremail."'>@</a>";
        } else $commentr['emaillink'] = '';
        
                
        if($comment['data']->pubwebsite > 0 && $comment['data']->posterwebsite != '') {
            $commentr['websitelink'] = "<a href='".$comment['data']->posterwebsite."'>www</a>";
        } else $commentr['websitelink'] = '';
                
        $commentr['websiteurl'] = $comment['data']->posterwebsite;
        $commentr['permalink'] = "<a name='comment{$comment['data']->commentid}'></a>
                          <a href='".$this->_get_comment_permalink($postid,$comment['data']->commentid)."'>#</a>";
    
        $commentr['permalinkurl'] = $this->_get_comment_permalink($postid,$comment['data']->commentid);
                    
        $commentr['replylinkurl'] = $this->_get_entry_permalink($postid);
        
            if(substr_count($commentr['replylinkurl'],"?") == 1) {
                    $commentr['replylinkurl'] .= "&amp;";
            } else {
                $commentr['replylinkurl'] .= "?";
            }
    
            $commentr['replylinkurl'] .= "replyto={$comment['data']->commentid}#commentform";
        
        $commentr['replylink'] = "<a href='".$commentr['replylinkurl']."'>Reply</a>";
    
        $commentr['commentid'] = $comment['data']->commentid;
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
    
    
    
    function makethread($parcat,$table,$level){
    
    // recursive function! Get your head around this! :
        global $finalar;
        if($level > $this->highestlevel) $this->highestlevel = $level;
        $list=$table[$parcat];
            while(list($key,$val)=each($list)){
        array_push($this->com_order_array,array("id"=>$val,"level"=>$level,"data"=>$this->com_finalar[$val]));
            if ((isset($table[$key]))){
                $this->makethread($key,$table,$level+1);
            }
        }
        return true;
    } // end function makethread
}
?>