<?php

/**
 * trackbackhandler.class.php - Implements trackback handling according to the Trackback specification found at http://www.sixapart.com/pronet/docs/trackback_spec
 *
 * @package Loquacity
 * @author Kenneth Power <telcor@users.berlios.de>, http://www.loquacity.info/ - last modified by $LastChangedBy: $
 * @version $Id: $
 * @copyright 2006 Kenneth Power <telcor@users.berlios.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
/* This file is part of Loquacity.
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
 
 
class trackbackhandler extends commentHandler {
    var $_db;
    var $_tbdata;
    var $_post;
    var $_ip;
    /**
     * Contructor
     * 
     * @param object $db
     * @param object $post The post receiving the trackback
     */
    function trackbackhandler(&$db, $post){
        commentHandler::commentHandler(&$db, $post);
    }
    /**
     * Process a trackback someone sent to us
     * 
     * @param string $ip IP Address of the pinger
     * @param array $ext_vars The trackback data, in the format:
     * +================================================+
     * | key       |   value                            |
     * +-----------+------------------------------------+
     * | url*      | URL of the pinging site            |
     * +-----------+------------------------------------+
     * | title     | Title of the referring article     |
     * +-----------+------------------------------------+
     * | excerpt   | Excerpt from the referring article |
     * +-----------+------------------------------------+
     * | blog_name | Name of the referring blog         |
     * +===========+====================================+
     * @param int $commentid If given, the ID of a comment in a blog
     */
    function receiveTrackback($ip, $ext_vars, $commentid = null){
        $this->_ip = $ip;
        $this->_tbdata = $ext_vars;
        $allow = $this->allowTrackback();
        if(is_array($allow)){
            foreach($allow['message'] as $msg)
                $err .= ' '.$msg;
            $this->userResponse(1,$msg);
        }
        else{
            $replyto = is_null($commentid) ? $commentid : 0;
            
            /*
             * According to the spec, only URL is required, all else is optional
             */
            $vars['posterwebsite'] = my_addslashes($this->_tbdata['url']);
            /**
             * Policy:
             *   In the interests of spam-blocking, the only hypertext we allow is the
             *   URL of the poster. This is the only deviance from comment handling
             */
            $vars['title'] = (isset($this->_tbdata['title'])) ? my_addslashes(StringHandling::removeTags($this->_tbdata['title'])) : '';
            $vars['commenttext'] = (isset($this->_tbdata['excerpt'])) ? my_addslashes(StringHandling::removeTags($this->_tbdata['excerpt'])) : '';
            $vars['postername'] = (isset($this->_tbdata['blog_name'])) ? my_addslashes(StringHandling::removeTags($this->_tbdata['blog_name'])) : '';
            $vars['posttime'] = time();
            $vars['ip'] = $this->_ip;
            $vars['postid'] = $this->_post->postid;
            if($replyto > 0)
                $vars['parentid'] = $replyto;
            
            /*
            * Added check for moderation.
            * Follow the same rules as for comments
            */
            $vars['commenttext'] = StringHandling::removeTags(my_addslashes($vars['commenttext']));
            $vars['onhold'] = ($this->needsModeration($vars['commenttext'])) ? 1 : 0;
            $vars['type'] = 'trackback';
            
            //Save the trackback
            $id = $this->saveComment($vars);
            if($id > 0) { 
                // notify owner
                if(C_NOTIFY == true){
                    $this->notify($vars['postername'], $this->_post->permalink, $vars['onhold'], $vars['commenttext']);
                }
                $this->updateCommentCount($this->_db, $this->_post->postid);
                $this->userResponse(0);
            } else {
                $this->userResponse(1,"Error adding trackback : ".mysql_error());
            }
        }
    }
    /**
     * Respond to a trackback ping
     * 
     * According to the specification:
     * <quote>
     * In the event of a succesful ping, the server MUST return a response in
     * the following format:
     * <code>
     * <?xml version="1.0" encoding="utf-8"?>
     * <response>
     *  <error>0</error>
     * </response>
     * </code>
     * In the event of an unsuccessful ping, the server MUST return an HTTP
     * response in the following format:
     * <code>
     * <?xml version="1.0" encoding="utf-8"?>
     * <response>
     * <error>1</error>
     * <message>The error message</message>
     * </response>
     * </code>
     * </quote>
     * 
     * @param int $err 0 or 1 only. 0 == Success; 1 == Failure
     * @param string $msg Only necessary when an error is raised. In this case
     * it is an error message
     * @return void
     */
    function userResponse($err, $msg){
        if($err !== 0 && $err !== 1){
            return 'Improper value given for trackback handling status';
        }
        else{
            $result = '<?xml version="1.0" encoding="utf-8"?'.">\n<response>\n<error>".$err."</error>\n";
            if(!empty($msg))
                $result .= "<message>".$msg."</message>\n";
            $result .= "</response>";
            if(!headers_sent())
                header("Content-Type: application/xml");
            return $result;
        }
    }
    /**
     * Performs various checks to determine whether the trackback is allowed.
     * 
     * Most checks are user-configurable via the admin options control panel.
     * The following are checked:
     *      Flooding
     *      User Input (comments, trackbacks) disallowed
     *      URL (a requirement of the spec)
     * @return mixed If a trackback is allowed, return true, else return an array of error messages
     */
    function allowTrackback(){
        $rval = true;
        $rs = array();
        if(!isset($this->_tbdata['url'])){
            $rs['message'][] = array("Error: No URL supplied. The trackback specification stipulates the URL is required.");
        }
        if($this->isDisabled($this->_post)){
            $rs['message'][] = array('Trackbacks are disabled for this post');
        }
        if($this->isFlooding($this->_db, $this->_ip, time())){
            $rs['message'][] = array("Error adding trackback. You tried to make a comment too soon after your last one. Please try again later. This is a bBlog spam prevention measure");
        }
        if(count($rs) > 0)
            $rval = $rs;
        return $rval;
    }
}
?>