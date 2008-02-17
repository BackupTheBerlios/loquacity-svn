<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Publishing
 * @author Kenneth Power <telcor@users.berlios.de>, Demian Turner
 * @copyright &copy; 2006, 2007 Kenneth Power, Demian Turner
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
 * Class for handling posts of the author (for example: add/delete posts, get existing posts)
 *
 * @version $Revision$
 */

class posthandler {

    function postHandler(&$db, $m_paths) {
        $this->_db = $db;
        $this->modifier_paths = $m_paths;
    }
    /**
	* inserts a new entry
	* returns the new entryid on success
	* error message on fail
	* assumes that my_addslashes() has already been applied and data is safe.
	*/
    function new_post($post){
        $this->newPost($post);
    }
    
    /**
    * Inserts a new article into the system
    *
    * When successful, the ID of the post is returned, otherwise FALSE is returned
    * and any error message is assigned to _last_error
    *
    * @param array $post The data from the post form
    * @return mixed
    */
    function newPost($post){
        $rval = false;
        $now = strtotime(gmdate("M d Y H:i:s"));
        $sections = ':';
        if(isset($post['frm_sections']) && count($post['frm_sections'])>0) {
            $sections = ':'.implode(":", $post['frm_sections']).':';
        }
        $title =  $post['frm_post_title'];
        $body =  $post['frm_post_body'];
        $posttime = (isset($post['posttime'])) ? $post['posttime'] : $now;
        $modifytime = $posttime;
        $status = $post['frm_post_status'];
        $modifier = $post['frm_modifier'];
        $ownerid = (isset($post['ownerid'])) ? intval($post['ownerid']) : $_SESSION['user_id'];
        $hidefromhome = (isset($post['frm_post_hidefromhome']) && $post['frm_post_hidefromhome'] == 1) ? 1: 0;
        $allowcomments = (isset($post['frm_post_allowcomments']) && $post['frm_post_allowcomments'] == ('allow' or 'disallow' or 'timed')) ? $post['frm_post_allowcomments'] : 'disallow';
        # TODO this needs refactored as everytime the post is edited, the disable date will auto-change (unintended). Make it use a definite date
        $autodisabledate = 'null';
        if(isset($post['disallowcommentsdays']) && in_array($post['disallowcommentsdays'], array(7, 14, 30, 90))){
        	$inc = $post['disallowcommentsdays'];
        	$rs['autodisabledate'] = strtotime("+$inc days");
        }
        $sql = 'INSERT INTO `'.T_POSTS.'` VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)';
        $stmt = $this->_db->Prepare($sql);
        $this->_db->debug = true;
        if($this->_db->Execute($stmt, array($title, $body, $posttime, $modifytime, $status, $modifier, $sections, $ownerid, $hidefromhome, $allowcomments, $autodisabledate )) !== false){
			$rval = intval($this->_db->insert_id());
            if(isset($post['send_trackback']) && $post['send_trackback'] == true){
            	include_once(LOQ_APP_ROOT.'includes/trackbackhandler.class.php');
            	$tb = new trackbackhandler($this->_db);
            	$tb->send_trackback('', $post['title'], $post['excerpt'], $post['tburl']);
            }
        }
        else{
            $this->_last_error = $this->_db->ErrorMsg();
        }
        return $rval;
        //return $this->modifyPost($post, 'INSERT');
    }
    
    /**
	 * Adds a new or edits an existing post
	 *
	 * @param array $post
	 * @param string $method Only accepts INSERT and UPDATE
	 * @param string $where Optional unless $method == UPDATE
	 * @return unknown
	 */
    function modifyPost($post){
    	if($method !== 'INSERT' && $method !== 'UPDATE'){
    		$this->_last_error = 'Unknown method stipulated for modifyPost';
    		return false;
    	}
    	$rval = false;
        $now = strtotime(gmdate("M d Y H:i:s"));
        $sections = ':';
        if(isset($post['frm_sections']) && count($post['frm_sections'])>0) {
            $sections = ':'.implode(":", $post['frm_sections']).':';
        }
        $rs['title'] = $post['frm_post_title'];
        $rs['body'] = $post['frm_post_body'];
        if($method !== 'UPDATE'){
	        $rs['posttime'] = (isset($post['posttime'])) ? $post['posttime'] : $now;
        }
        $rs['modifytime'] = (isset($post['modifytime'])) ? $post['modifytime'] : $now;
        $rs['status'] = $post['frm_post_status'];
        $rs['modifier'] = $post['frm_modifier'];
        $rs['sections'] = $sections;
        $rs['ownerid'] = (isset($post['ownerid'])) ? intval($post['ownerid']) : $_SESSION['user_id'];
        $rs['hidefromhome'] = (isset($post['frm_post_hidefromhome']) && $post['frm_post_hidefromhome'] == 1) ? 1: 0;
        $rs['allowcomments'] = (isset($post['frm_post_allowcomments']) && $post['frm_post_allowcomments'] == ('allow' or 'disallow' or 'timed')) ? $post['frm_post_allowcomments'] : 'disallow';
        # TODO this needs refactored as everytime the post is edited, the disable date will auto-change (unintended). Make it use a definite date
        if(isset($post['disallowcommentsdays']) && in_array($post['disallowcommentsdays'], array(7, 14, 30, 90))){
        	$inc = $post['disallowcommentsdays'];
        	$rs['autodisabledate'] = strtotime("+$inc days");
        }
        
        if($this->_db->AutoExecute(T_POSTS, $rs, $method, $where, false, get_magic_quotes_runtime()) !== false){
            if($method === 'INSERT' ){
                $rval = intval($this->_db->insert_id());
            }
            else{
                $rval = true;
            }
            if(isset($post['send_trackback']) && $post['send_trackback'] == true){
            	include_once(LOQ_APP_ROOT.'includes/trackbackhandler.class.php');
            	$tb = new trackbackhandler($this->_db);
            	$tb->send_trackback('', $post['title'], $post['excerpt'], $post['tburl']);
            }
        }
        else{
            $this->_last_error = $this->_db->ErrorMsg();
        }
        return $rval;
    }
    
    /**
	 * Retrieve one or most posts based upon selection criteria. May stipulate wether to process it for display purpose
	 *
	 * @param array $crit Hash of selection criteria
	 * @param string $process Prepare the data for use in special process. Valid arguments:
	 *	none	Do not process
	 * 	html	Make suitable for web display
	 *
	 * @return mixed False on error, arroy of posts on success
	 */
    function get_posts($crit, $process='none') {
        if(is_array($crit)){
            $rs = $this->_db->Execute($this->make_post_query($crit));
            if($rs !== false && !$rs->EOF){
                $posts = array();
                while($p = $rs->FetchRow()){
                    $posts[] = $this->get_post($p['postid'], false, $process);
                }
                return $posts;
            }
            else{
                $this->status = "No posts found";
                return false;
            }
        }
        else{
            $this->status = 'No selection criteria supplied';
            return false;
        }
    }

    /**
	 * Prepares a post for HTML display
	 * 
	 * @param array $post
	 * @return array
	 */
    function processForHtmlDisplay($post) {
		$npost = array();
    	$npost['id'] = $post['postid'];
        $npost['postid'] = $post['postid'];
        $npost['permalink'] = (defined('CLEANURLS')) ? str_replace('%postid%',$post['postid'],URL_POST) : BLOGURL.'?postid='.$post['postid'];
        $npost['trackbackurl'] = (defined('CLEANURLS')) ?  BLOGURL.'trackback/tbpost='.$post['postid'] : BLOGURL.'trackback.php&amp;tbpost='.$post['postid'];
        $npost['title'] = htmlspecialchars($post['title']);
        if($post['modifier'] != '') {
            $npost['body'] = $this->apply_modifier($post['modifier'], $post['body']);
            // replace only &'s that are by themselves, else we risk converting an entity
            $npost['body'] = str_replace(' & ', ' &amp; ', $npost['body']);
        }
        $npost['status'] = $post['status'];
        $npost['posttime'] = $post['posttime'];
        $npost['modifytime'] = $post['modifytime'];

        // what we need here is that the date format
        // is available in the control panel as an option
        // this is only here as a convience, the date_format modifier should be used.
        //$npost['posttime_f'] = date("D M j G:i:s T Y",$post['posttime']);
        //$npost['modifytime_f'] = date("D M j G:i:s T Y",$post['modifytime']);
        switch(intval($post['NUMCOMMENTS'])) {
             case 1 : $npost['commenttext'] = "One comment"; break;
             case 0 : $npost['commenttext'] = "Add Comment"; break;
             default: $npost['commenttext'] = $post['NUMCOMMENTS'] ." comments"; break;
        }
        $npost['commentcount'] = $post['NUMCOMMENTS'];
        //TODO move this to a section module
		$npost['sections']   = array();
        if($post['sections'] != '') {
            //Transform the 'list' of sections into an array for use in a SQL statement
			$tmp_sec_ar = explode(":",trim($post['sections']));
			array_shift($tmp_sec_ar);
			array_pop($tmp_sec_ar);
			$sect = join(',', $tmp_sec_ar);
			$sql = 'SELECT * FROM `'.T_SECTIONS.'` WHERE sectionid IN ('.$sect.')';
			$rs = $this->_db->Execute($sql);
			if($rs){
				while($section = $rs->FetchRow()){
					$npost['sections'][] = array('id' => $section['sectionid'], 'nicename' => $section['nicename']); //, 'url' => $section['url']);
				}
			}
        }
        //add the author info
        $npost['author'] = array(
             'id' => $post['ownerid'],
             'nickname' => htmlspecialchars($post['nickname']),
             'email' => htmlspecialchars($post['email']),
             'fullname' => htmlspecialchars($post['fullname']));
        $npost['hidefromhome'] = $post['hidefromhome'];
        $npost['autodisabledate'] = $post['autodisabledate'];
        if($post['allowcomments'] == 'disallow' || ($post['allowcomments'] == 'timed' and $post['autodisabledate'] < time())){
            $npost['allowcomments'] = FALSE;
        }
        else {
            $npost['allowcomments'] = TRUE;
        }
        return $npost;
    }

    /**
	 * Build a SQL statement from specified criteria
	 *
	 * @param array $params Criteria used to build SQL statement
	 * @return string
	 */
    function make_post_query($params) {
    	$args = array();
        $skip           = (isset($params['skip'])) ? $params['skip'] : 0;
        $num            = (isset($params['num'])) ? $params['num'] : 20;
        $sectionid      = (isset($params['sectionid'])) ? $params['sectionid'] : FALSE;
        $postid         = (isset($params['postid'])) ? $params['postid'] : FALSE;
        $args['status']         = (isset($params['wherestart'])) ? $params['wherestart'] : 'status="live"';
        $column         = (isset($params['what'])) ? $params['what'] : "*";
        $order          = (isset($params['order'])) ? $params['order'] : "ORDER BY posts.posttime desc";
        $home           = (isset($params['home'])) ? intval($params['home']) : 0;

		$limit = 'LIMIT '.$skip.','.$num;
        if (isset($params['year'])){
        	$args['year'] = 'FROM_UNIXTIME(posts.posttime,"%Y") = '. intval($params['year']);
        }
        if (isset($params['month'])){
        	$m = intval($params['month']);
        	if($m > 0 && $m < 13){
        		$args['month'] = 'FROM_UNIXTIME(posts.posttime,"%m") = ' . $m;
        	}
        }
        if (isset($params['day'])){
        	$d = intval($params['day']);
        	if($d > 0 && $d < 32){
        		$args['day'] = 'FROM_UNIXTIME(posts.posttime,"%D") = ' . $d;
        	}
        }
        if (isset($params['hour'])){
        	$h = intval($params['hour']);
        	if($h >= 0 && $h < 24){
        		$args['hour'] = 'FROM_UNIXTIME(posts.posttime,"%H") = ' . $h;
        	}
        }
        if (isset($params['minute'])){
        	$m = intval($params['minute']);
        	if ( $m >=0 && $m < 60){
        		$args['minute'] = 'FROM_UNIXTIME(posts.posttime"%i") = ' . $m;
        	}
        }
        if (isset($params['second'])){
        	$s = intval($params['second']);
        	if($s >= 0 && $s < 60){
        		$args['second'] = 'FROM_UNIXTIME(posts.posttime,"%S") = ' . $s;
        	}
        }
		$where = 'WHERE '.join(' AND ', $args);
		
        // There should be a ":" at the beginning and end of
        // any sections list
        if ((isset($sectionid)) && ($sectionid != FALSE))   $where .= " AND sections like '%:$sectionid:%' ";

        if($home) $where .= " AND hidefromhome=0";
        //$q = "SELECT posts.$what, authors.nickname, authors.email, authors.fullname, COUNT(`comments`.`commentid`) AS NUMCOMMENTS FROM ".T_POSTS." AS posts LEFT JOIN ".T_AUTHORS." AS authors ON posts.ownerid = authors.id LEFT JOIN `".T_COMMENTS."` as comments ON posts.postid = comments.postid $wherestart $where GROUP BY posts.postid $order $limit";
        return"SELECT posts.postid FROM `".T_POSTS."` AS posts LEFT JOIN ".T_AUTHORS." AS authors ON posts.ownerid = authors.id $where GROUP BY posts.postid $order $limit";
    }


    /**
	 * Retrieves a single post
	 *
	 * @param int $postid
	 * @param bool $draftok If true, its ok to retrieve a draft post
	 * @param string $process Prepare data for use in special processes
	 * @return mixed
	 */
    function get_post ($postid, $draftok = FALSE, $process = 'none'){
		$process = strtolower($process);
        if (!is_numeric($postid))
            return false;
		$stmt = '';
        if (!$draftok){
            $stmt = $this->_db->Prepare("SELECT posts.*, authors.nickname, authors.email, authors.fullname, COUNT(comments.commentid) AS NUMCOMMENTS FROM `".T_POSTS."` AS posts LEFT JOIN `".T_AUTHORS."` AS authors ON posts.ownerid = authors.id LEFT JOIN `".T_COMMENTS."` AS comments ON posts.postid = comments.postid WHERE posts.postid=? AND posts.status='live' GROUP BY posts.postid");
		}
        else{
            $stmt = $this->_db->Prepare("SELECT posts.*, authors.nickname, authors.email, authors.fullname, COUNT(comments.commentid) AS NUMCOMMENTS FROM `".T_POSTS."` AS posts LEFT JOIN `".T_AUTHORS."` AS authors ON posts.ownerid = authors.id LEFT JOIN `".T_COMMENTS."` AS comments ON posts.postid = comments.postid WHERE posts.postid=? GROUP BY posts.postid");
		}
        $post = $this->_db->GetRow($stmt, array($postid));
        if(!$post){
            $this->status = 'No post found';
            return false;
        }
        if($process == 'none')
            return $post;
        else{
            return $this->processForHtmlDisplay($post);
        }
    }

    /**
	 * Removes a post and all associated comments
	 * 
	 * @param int $postid
	 * @return void
	 */
    function delete_post($postid) {
        $postid = intval($postid);
        if(!is_int($postid) && $postid <= 0) return false;
        $q1 = "DELETE FROM ".T_COMMENTS." WHERE postid='$postid'";
        $this->_db->Execute($q1);
        $q2 = "DELETE FROM ".T_POSTS." WHERE postid='$postid'";
        $this->_db->Execute($q2);
    }

	/**
	 *  Wrapper for modifyPost that sets parameters for post updating
	 * 
	 * @see posthandler::modifyPost
	 * @param array $params Post data
	 * @return mixed
	 */
    function edit_post($params) {
		if ((isset($params['edit_timestamp'])) && ($params['edit_timestamp'] == 'TRUE')){
			$now = getdate(strtotime(gmdate("M d Y H:i:s")));
			//If user forgot to modify timestamp, we set it to NOW
			$params['ts_day'] =  (isset($params['ts_day'])) ? $params['ts_day'] : $now['mday'];
			$params['ts_month'] =  (isset($params['ts_month'])) ? $params['ts_day'] : $now['mon'];
			$params['ts_year']  = (isset($params['ts_year'])) ? $params['ts_year'] : $now['year'];
			$params['ts_hour']  = (isset($params['ts_hour'])) ? $params['ts_year'] : $now['hours'];
			$params['ts_minute']  = (isset($params['ts_minute'])) ? $params['ts_minute'] : $now['minutes'];
			$timestamp = mktime($params['ts_day'],$params['ts_month'],$params['ts_year'],$params['ts_hour'],$params['ts_minute']);
			$params['posttime'] = $timestamp;
		}
		return $this->modifyPost($params, 'UPDATE', 'postid='.intval($params['postid']));
    }
    
    /**
	 * Returns the direct link to a post
	 * 
	 * @param int $postid
	 * @return string
	 */
    function get_post_permalink($postid){
        if(defined('CLEANURLS')){
            return str_replace('%postid%',$postid,URL_POST);
        }
        else{
            return BLOGURL.'?postid='.$postid;
        }
    }
    /**
	 * Loads and applies a Smarty based modifier to a post
	 *
	 * TODO This is a hack and should be better designed
	 * 
	 * @param array $post
	 * @return array
	 */
    function apply_modifier($mod, $body){
        $modifier = 'modifier.'.$mod.'.php';
        if(($m_path = $this->find_path($modifier)) !== false){
            require_once($m_path);
            $mod_func = 'smarty_modifier_'.$mod;
            $body = $mod_func($body);
            //$post['applied_modifier'] = $mod;
        }
        return $body;
    }
    /**
	* Searches the modifier paths looking for the requested modifier
	*
	* @param string $modifier File name of modifier
	* @return mixed Fullty qualified path on success; False on failure
	*/
    function find_path($modifier){
        foreach($this->modifier_paths as $path){
            $f_path = $path.'/'.$modifier;
            if(file_exists($f_path))
                return $f_path;
        }
        return false;
    }
    function lastError(){
        return $this->_last_error;
    }
    function getBySection($section){
    	if(is_int($section) && $section > 0){
    		$sql = 'SELECT posts.postid FROM `'.T_POSTS.'` as posts WHERE posts.sections LIKE "%'.$section.'%"';
    		return $this->_db->Execute($sql);
    	}
    }
}
?>