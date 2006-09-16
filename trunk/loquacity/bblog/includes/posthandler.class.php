<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Publishing
 * @author Kenneth Power <telcor@users.berlios.de>, Demian Turner
 * @copyright &copy; 2006 Kenneth Power, Demian Turner
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
    function new_post($post) {
        return $this->modifyPost($post, 'INSERT');
    }
    function modifyPost($post, $method='INSERT', $where=null){
	$now = time();
        $sections = ':';
        if(count($post['frm_sections'])>0) {
            $sections = ':'.implode(":", $post['frm_sections']).':';
        }
        $rs['title'] = stringHandler::removeMagicQuotes($post['frm_post_title']);
        $rs['body'] = stringHandler::removeMagicQuotes($post['frm_post_body']);
        $rs['posttime'] = (isset($post['posttime'])) ? $post['posttime'] : $now;
        $rs['modifytime'] = (isset($post['modifytime'])) ? $post['modifytime'] : $now;
        $rs['status'] = $post['frm_post_status'];
        $rs['modifier'] = $post['frm_modifier'];
        $rs['sections'] = $sections;
        $rs['ownerid'] = (isset($post['ownerid'])) ? intval($post['ownerid']) : $_SESSION['user_id'];
        $rs['hidefromhome'] = ($post['frm_post_hidefromhome'] == 1) ? 1: 0;
        $rs['allowcomments'] = ($post['frm_post_allowcomments'] == ('allow' or 'disallow' or 'timed')) ? $post['frm_post_allowcomments'] : 'disallow';
        $rs['autodisabledate'] = (is_numeric($post['frm_post_autodisabledate'])) ? intval($post['frm_post_autodisabledate']) : '';
        if($this->_db->AutoExecute(T_POSTS, $rs, $method, $where) !== false){
            return $this->_db->insert_id;
        }
        else{
            $this->_last_error = $this->_db->ErrorMsg();
            return false;
        }
    }
    /**********************************************************************
    ** get_entries
    ** Gets blog entries from the db
    ** array, $limit ex. " LIMIT 0,20 ", $order ex. " ORDER BY tstamp desc "
    ** $sectionid ex = 1
    ** Return
    **********************************************************************/

    /**
     * Retrieve one or most posts based upon criteria
     *
     * @param array $crit Hash of selection criteria
     * @param bool $raw If false return in a format sufficient for browser display
     * @return mixed False on error, arroy of posts on success
     */
    function get_posts($crit, $raw=FALSE) {
        if(is_array($crit)){
            $rs = $this->_db->Execute($this->make_post_query($crit));
            if($rs !== false && !$rs->EOF){
                $posts = array();
                while($p = $rs->FetchRow()){
                    $posts[] = $this->get_post($p['postid'], false, $raw);
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
    * !formats a single post into a useful array suitable for smarty
    * i.e. an associatve array not an object
    * this function is pretty basic at the moment, but all
    * sorts of things will happen in the future.
    * it assumes that the required plugin modifiers have been loaded
    */
    function prep_post(&$post) {
        //first do the basics

        $npost['id'] = $post['postid'];
        $npost['postid'] = $post['postid'];
        $npost['permalink'] = (defined(CLEANURLS)) ? str_replace('%postid%',$post['postid'],URL_POST) : BLOGURL.'?postid='.$post['postid'];
        $npost['trackbackurl'] = (defined(CLEANURLS)) ?  BLOGURL.'trackback/tbpost='.$post['postid'] : BBLOGURL.'trackback.php&amp;tbpost='.$post['postid'];
        $npost['title'] = htmlspecialchars($post['title']);
        //var_dump($npost['permalink']);
        // do the body text
        if($post['modifier'] != '') {
            $npost['body'] = $this->apply_modifier($post['modifier'], $post['body']);
            // replace only &'s that are by themselves, else we risk converting an entity
            $npost['body'] = str_replace(' & ', ' &amp; ', $npost['body']);
        }
        $npost['status'] = $post['status'];

        // in the future
        $npost['posttime'] = $post['posttime'];
        $npost['modifytime'] = $post['modifytime'];

        // what we need here is that the date format
        // is available in the control panel as an option
        // this is only here as a convience, the date_format modifier should be used.
        $npost['posttime_f'] = date("D M j G:i:s T Y",$post['posttime']);
        $npost['modifytime_f'] = date("D M j G:i:s T Y",$post['modifytime']);
        $npost['sections']   = array();
        switch(intval($post['NUMCOMMENTS'])) {
             case 1 : $npost['commenttext'] = "One comment"; break;
             case 0 : $npost['commenttext'] = "Add Comment"; break;
             default: $npost['commenttext'] = $post['NUMCOMMENTS'] ." comments"; break;
        }
        $npost['commentcount'] = $post['NUMCOMMENTS'];
        //TODO move this to a section module
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
                      $npost['sections'][] = array('id' => $section['sectionid'], 'nicename' => $section['nicename'], 'url' => $section['url']);
                  }
              }
        }
        //add the author info
        $npost['author'] = array(
             'id' => $post['ownerid'],
             'nickname' => $post['nickname'],
             'email' => $post['email'],
             'fullname' => $post['fullname']);
        $npost['hidefromhome'] = $post['hidefromhome'];
        $npost['autodisabledate'] = $post['autodisabledate'];
        if($post['allowcomments'] == 'disallow' or ($post['allowcomments'] == 'timed' and $post['autodisabledate'] < time())){
            $npost['allowcomments'] = FALSE;
        }
        else {
            $npost['allowcomments'] = TRUE;
        }

        return $npost;
    }

    function make_post_query($params) {
        $skip           = 0;
        $num            = 20;
        $sectionid      = FALSE;
        $postid         = FALSE;
        $wherestart     = " WHERE status='live' ";
        $where          = "";
        $order          = " ORDER BY posts.posttime desc ";
        $what           = "*";

        // overwrite the above defaults with options from the $params array
        extract($params);

        if (!isset($limit))                                 $limit = " LIMIT $skip,$num ";
        if ((isset($postid)) && ($postid != FALSE))         $where .= " AND postid='$postid' ";
        if (isset($year))                                   $where .= " AND FROM_UNIXTIME(posttime,'%Y') = '" . addslashes($year) . "' ";
        if (isset($month))                                  $where .= " AND FROM_UNIXTIME(posttime,'%m') = '" . addslashes($month) . "' ";
        if (isset($day))                                    $where .= " AND FROM_UNIXTIME(posttime,'%D') = '" . addslashes($day) . "' ";
        if (isset($hour))                                   $where .= " AND FROM_UNIXTIME(posttime,'%H') = '" . addslashes($hour) . "' ";
        if (isset($minute))                                 $where .= " AND FROM_UNIXTIME(posttime,'%i') = '" . addslashes($minute) . "' ";
        if (isset($second))                                 $where .= " AND FROM_UNIXTIME(posts.posttime,'%S') = '" . addslashes($second) . "' ";

        // There should be a ":" at the beginning and end of
        // any sections list
        if ((isset($sectionid)) && ($sectionid != FALSE))   $where .= " AND sections like '%:$sectionid:%' ";

        if($home) $where .= " AND hidefromhome=0 ";
        //$q = "SELECT posts.$what, authors.nickname, authors.email, authors.fullname, COUNT(`comments`.`commentid`) AS NUMCOMMENTS FROM ".T_POSTS." AS posts LEFT JOIN ".T_AUTHORS." AS authors ON posts.ownerid = authors.id LEFT JOIN `".T_COMMENTS."` as comments ON posts.postid = comments.postid $wherestart $where GROUP BY posts.postid $order $limit";
        $q = "SELECT posts.postid FROM ".T_POSTS." AS posts LEFT JOIN ".T_AUTHORS." AS authors ON posts.ownerid = authors.id LEFT JOIN `".T_COMMENTS."` as comments ON posts.postid = comments.postid $wherestart $where GROUP BY posts.postid $order $limit";
        return $q;
    }


    /**
     * Retrieves a single post
     *
     * @param int $postid
     * @param bool $draftok If true, its ok to retrieve a draft post
     * @param bool $raw If true, don't prepare for display
     * @return mixed
     */
    function get_post ($postid, $draftok = FALSE, $raw = FALSE){
        if (!is_numeric($postid))
            return false;
        // this makes it safe for general use.
        // we don't want ppl being able to view drafts.
        if (!$draftok)
            $draft_q = "AND posts.status='live' ";
        else
            $draft_q = '';
        $q = "SELECT posts.*, authors.nickname, authors.email, authors.fullname, COUNT(comments.commentid) AS NUMCOMMENTS FROM `".T_POSTS."` AS posts LEFT JOIN `".T_AUTHORS."` AS authors ON posts.ownerid = authors.id LEFT JOIN `".T_COMMENTS."` AS comments ON posts.postid = comments.postid WHERE posts.postid='$postid' $draft_q GROUP BY posts.postid LIMIT 0,1";
        $post = $this->_db->GetRow($q);
        if(!$post){
            $this->status = 'No post found';
            return false;
        }
        if($raw)
            return $post;
        else{
            #require_once $this->_get_plugin_filepath('modifier', $post->modifier);
            return $this->prep_post($post);
        }
    }

    ////
    // !deletes a post
    function delete_post($postid) {
        if(!is_numeric($postid)) return false;
        $this->modifiednow();
        // delete comments
        $q1 = "DELETE FROM ".T_COMMENTS." WHERE postid='$postid'";
        $this->_db->Execute($q1);
        // delete post
        $q2 = "DELETE FROM ".T_POSTS." WHERE postid='$postid'";
        $this->_db->Execute($q2);
    }

    /**
    * TODO Make a way to keep the prior timestamp
    */
    function edit_post($params) {
	if ((isset($params['edit_timestamp'])) && ($params['edit_timestamp'] == 'TRUE')){
		// the timestamp will be changed.
		$params['ts_day'] =  (isset($params['ts_day'])) ? $params['ts_day'] : 0;
		$params['ts_month'] =  (isset($params['ts_month'])) ? $params['ts_day'] : 0;
		$params['ts_year']  = (isset($params['ts_year'])) ? $params['ts_year'] : 0;
		$params['ts_hour']  = (isset($params['ts_hour'])) ? $params['ts_year'] : 0;
		$params['ts_minute']  = (isset($params['ts_minute'])) ? $params['ts_minute'] : 0;
		$timestamp = maketimestamp($params['ts_day'],$params['ts_month'],$params['ts_year'],$params['ts_hour'],$params['ts_minute']);
		$params['posttime'] = $timestamp;
	}
	return $this->modifyPost($params, 'UPDATE', 'postid='.intval($params['postid']));
    }
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
}
?>
