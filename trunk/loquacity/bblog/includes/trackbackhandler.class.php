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
 * Implements trackback handling according to the Trackback specification found at http://www.sixapart.com/pronet/docs/trackback_spec
 *
 * @version $Revision$
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
     * @param int $post The post receiving the trackback
     */
    function trackbackhandler(&$db, $post=null){
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
        if(!isset($this->_comments) || count($this->_comments) == 0){
            $this->userResponse(1, 'This post does not exist');
        }
        else{
            $this->_ip = $ip;
            $this->_tbdata = $ext_vars;
            if(is_array(($allow = $this->allowTrackback()))){
                $msg = join($allow, ',');
                $this->userResponse(1,$msg);
            }
            else{
                $trackback = $this->prepFieldsForDB($commentid);
                //Save the trackback
                $id = $this->saveComment($trackback);
                if($id > 0) {
                    // notify owner
                    /*if(C_NOTIFY == true){
                        $this->notify($trackback['postername'], 'permalink', $trackback['onhold'], $trackback['commenttext']);
                    }*/
                    //$this->updateCommentCount($this->_db, $this->_post->postid);
                    $this->userResponse(0, '');
                } else {
                    $this->userResponse(1,"Error adding trackback : ".mysql_error());
                }
            }
        }
    }
    /**
     * Prepare trackback data for storage in the database
     *
     * @param int   $commentid If supplied, the id of the comment being replied to
     * @return array
     */
    function prepFieldsForDB($commentid = null){
        $replyto = is_null($commentid) ? $commentid : 0;
        /*
            * According to the spec, only URL is required, all else is optional
            */
        $vars['posterwebsite'] = ($this->_tbdata['url']);
        /**
        * Policy:
        *   In the interests of spam-blocking, the only hypertext we allow is the
        *   URL of the poster. This is the only deviance from comment handling. This means no URL transformation is performed
        */
        $vars['title'] = (isset($this->_tbdata['title'])) ? (stringHandler::clean($this->_tbdata['title'])) : '';
        $vars['commenttext'] = (isset($this->_tbdata['excerpt'])) ? (stringHandler::clean($this->_tbdata['excerpt'])) : '';
        $vars['postername'] = (isset($this->_tbdata['blog_name'])) ? (stringHandler::clean($this->_tbdata['blog_name'])) : '';
        $vars['posttime'] = time();
        $vars['ip'] = $this->_ip;
        $vars['postid'] = $this->_post;
        if($replyto > 0)
            $vars['parentid'] = $replyto;

        /*
        * Added check for moderation.
        * Follow the same rules as for comments
        */
        $vars['commenttext'] = stringHandler::clean(($vars['commenttext']));
        $vars['onhold'] = ($this->needsModeration($vars['commenttext'])) ? 1 : 0;
        $vars['type'] = 'trackback';
        return $vars;
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
    function userResponse($err, $msg=null){
        if($err !== 0 && $err !== 1){
            return 'Improper value given for trackback handling status';
        }
        else{
            $result = '<?xml version="1.0" encoding="utf-8"?'.">\n<response>\n<error>".$err."</error>\n";
            if(!is_null($msg) && $msg !== '')
                $result .= "<message>$msg</message>\n";
            $result .= '</response>';
            if(!headers_sent())
                header("Content-Type: application/xml");
            echo $result;
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
            $rs[] = "Error: No URL supplied. The trackback specification stipulates the URL is required.";
        }
        if($this->isDisabled($this->_post)){
            $rs[] = 'Trackbacks are disabled for this post';
        }
        if($this->isFlooding($this->_db, $this->_ip, time())){
            $rs[] = "Error adding trackback. You tried to make a comment too soon after your last one. Please try again later. This is a bBlog spam prevention measure";
        }
        if(count($rs) > 0)
            $rval = $rs;
        return $rval;
    }
	// Send a trackback-ping.
	function send_trackback($url, $title="", $excerpt="",$t) {
		//parse the target-url
		$target = parse_url($t);
		
		if ($target["query"] != "") $target["query"] = "?".$target["query"];
		
		//set the port
		if (!is_numeric($target["port"])) $target["port"] = 80;
		
		//connect to the remote-host
		$fp = fsockopen($target["host"], $target["port"]);
		
		if ($fp){		
			// build the Send String
			$Send = "url=".rawurlencode($url).
				"&title=".rawurlencode($title).
				"&blog_name=".rawurlencode(C_BLOGNAME).
				"&excerpt=".rawurlencode($excerpt);
		
			// send the ping
			fputs($fp, "POST ".$target["path"].$target["query"]." HTTP/1.1\n");
			fputs($fp, "Host: ".$target["host"]."\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
			fputs($fp, "Content-length: ". strlen($Send)."\n");
			fputs($fp, "Connection: close\n\n");
			fputs($fp, $Send);
		
			//read the result
			while(!feof($fp)) {
				$res .= fgets($fp, 128);
			}
			//close the socket again
			fclose($fp);
			//return success
			return true;
		}else{
		
			//return failure
			return false;
		}
	
	}

}
?>