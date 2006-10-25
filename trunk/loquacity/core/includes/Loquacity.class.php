<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Administration
 * @author Kenneth Power <telcor@users.berlios.de>, Eaden McKee <email@eadz.co.nz>
 * @copyright &copy; 2006 Kenneth Power, 2003  Eaden McKee
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
 * A multifunctional class
 *
 * @version $Revision$
 */

class Loquacity extends Smarty {
	var $template;
	var $num_homepage_entries = 20;
	var $templatepage = "index.html";
    // for comments
	var $highestlevel = 0;
	var $com_order_array = array();
	var $com_finalar;
    ////
    // !loq constructor function
	function Loquacity() {
        $this->__init();
        $this->get_sections();
        //Load the config
        $config =& new configHandler($this->_adb);
        $config->loadConfig();
        $this->assign('blogname',C_BLOGNAME);
        $this->assign('blogdescription',C_BLOG_DESCRIPTION);
        $this->assign('blogurl',BLOGURL);
        $this->assign('bblogurl',LOQ_APP_URL);
        $this->assign('metakeywords',C_META_KEYWORDS);
        $this->assign('metadescription',C_META_DESCRIPTION);
        $this->assign('charset',C_CHARSET);
        $this->assign('direction', C_DIRECTION);
        $this->assign('C_CAPTCHA_ENABLE', C_CAPTCHA_ENABLE);
        
        Smarty::Smarty();
        //Smarty setup
        $this->template_dir = LOQ_APP_ROOT.'templates/'.C_TEMPLATE;
        $this->compile_dir = LOQ_APP_ROOT.'generated/templates/';
        $this->plugins_dir = array(LOQ_APP_ROOT.'plugins', LOQ_APP_ROOT.'plugins/smarty',LOQ_APP_ROOT.'3rdparty/smarty/libs/plugins');
        $this->use_sub_dirs	= FALSE; // change to true if you have a lot of templates
        $this->_ph =& new postHandler($this->_adb, $this->plugins_dir);
        // initial time from config table, based on last updated stuff.
        // this is just the initial value.
        $this->lastmodified = C_LAST_MODIFIED;
        //$this->register_postfilter("update_when_compiled");
        // load up the sections
        

        //start the session that we need so much ;)
        if(!session_id()) {
	  	    session_start();
	    }
        $mtime = explode(" ",microtime());
        $this->begintime = $mtime[1] + $mtime[0];
        $this->__load_configuration();
	} // end of function loq
    
    function __init(){
        // connect to database
        $this->_adb = NewADOConnection('mysql://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.'/'.DB_DATABASE.'?persist');
        
    }
    function __load_configuration(){
        
        
    }
    /**
     * A place holder for calling $this->_ph->new_post
     */
    function new_post($post){
        $pid = $this->_ph->new_post($post);
        if(is_int($pid) && $pid > 0)
            $this->modifiednow();
    }

	/**********************************************************************
	** get_archives
	** Get a list of archives from the db
	**********************************************************************/
	function get_archives($opts) {
		$where = '';
		
		switch ($opts['show']) {
			case 'years':
				$archformat = '%Y';
				break;
			case 'months':
				$archformat = '%Y%m';
				break;
			case 'days':
				$archformat = '%Y%m%d'; 
				break;
			case 'hours':
				$archformat = '%Y%m%d%H';
				break;
			case 'minutes':
				$archformat = '%Y%m%d%H%i';
				break;
			case 'seconds':
				$archformat = '%Y%m%d%H%i%s';
				break;
			default:
				$archformat = '%Y%m';
				break;
		}

		if($opts['year'] != '') {
			$where .= " AND FROM_UNIXTIME(posttime, '%Y') = '" . addslashes($opts['year']) . "' ";
		}
		if($opts['month'] != '') {
			$where .= " AND FROM_UNIXTIME(posttime, '%m') = '" . addslashes($opts['month']) . "' ";
		}
		if($opts['day'] != '') {
			$where .= " AND FROM_UNIXTIME(posttime, '%d') = '" . addslashes($opts['day']) . "' ";
		}
		if($opts['hour'] != '') {
			$where .= " AND FROM_UNIXTIME(posttime, '%H') = '" . addslashes($opts['hour']) . "' ";
		}
		if($opts['minute'] != '') {
			$where .= " AND FROM_UNIXTIME(posttime, '%i') = '" . addslashes($opts['minute']) . "' ";
		}
		if($opts['second'] != '') {
			$where .= " AND FROM_UNIXTIME(posttime, '%s') = '" . addslashes($opts['second']) . "' ";
		}

		if($opts['sectionid'] != '') {
			$where .= " AND sections LIKE '%:" . addslashes($opts['sectionid']) . ":%' ";
		}


		if($opts['count'] == true) {
			$stmt = "select DISTINCT FROM_UNIXTIME(posttime, '" . $archformat . "') as archname, count(*) as cnt from ".T_POSTS." where status = 'live' " . $where . " group by archname order by archname";
		} else {
			$stmt = "select DISTINCT FROM_UNIXTIME(posttime, '" . $archformat . "') as archname from ".T_POSTS." where status = 'live' " . $where . " order by archname";
		}
		//echo $stmt;
        $rs = $this->_adb->Execute($stmt);
        if($rs !== false && !$rs->EOF){
            $ret = array();
            while($arch = $rs->FetchRow()){
                $year = substr($arch['archname'],0,4);
                $month = substr($arch['archname'],4,2);
                $day = substr($arch['archname'],6,2);
                $hour = substr($arch['archname'],8,2);
                $minute = substr($arch['archname'],10,2);
                $second = substr($arch['archname'],12,2);
                $ts = mktime(
                    $hour ? $hour : 0, 
                    $minute ? $minute : 0,
                    $second ? $second : 0,
                    $month ? $month : 1,
                    $day ? $day : 1,
                    $year ? $year : 1970);
                $ret[] = array(
                    'archname' => $arch['archname'],
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                    'hour' => $hour,
                    'minute' => $minute,
                    'second' => $second,
                    'ts' => $ts,
                    'count' => $arch['cnt']
                );
            }
        }
        else{
			return false;
		}
		return $ret;
	}

	/**
     * Authenticate the user
     * 
     * @param string $user Username
     * @param string $pass Password
     * @param bool   $setcookie If true, set a cookie
	 */
    function userauth($user, $pass, $setcookie = FALSE) {
        $query = "SELECT `id` FROM `".T_AUTHORS."` WHERE `nickname`='".stringHandler::removeMagicQuotes($user)."' AND `password`='".stringHandler::removeMagicQuotes(passwordManager::toSHA1($pass))."'";
        $rs = $this->_adb->GetRow($query);
        if($rs){
            $_SESSION['user_id'] = $rs[0];
            return true;
        }
        else {
            return false;
        }
    }
    ////
    // !logs out the admin
    function admin_logout() {
        $_SESSION['user_id'] = 0;
    }
    ////
    // !checks if the admin is logged in or not
    function admin_logged_in() {
        if ((isset($_SESSION['user_id'])) && ($_SESSION['user_id'] > 0))
            return true;
        else
            return false;
    }
	

	////
	// !in charge of printing any HTTP headers, and displaying the page
    // via $Smarty->display() and outputting the footer ( html comments ).
	// in the future if gzip is supported, it will happen here too.
    // Nothing should be sent to the browser except by this function!
    // and it really should only be called once.
	function display($page,$addfooter=true) {
        ob_start();
		parent::display($page);
        $o = ob_get_contents();
        ob_end_clean();
        echo $o;
        if($addfooter){
            echo buildfoot();
        }
	}

    /**
    *
    *
    */
    function get_sections(){
        $sh =& new sectionhandler($this->_adb);
        if(($s = $sh->getallsections()) !== false){
            $this->assign_by_ref('sections', $s);
            $this->sections = $s;
        }
	}
	
    ////
    // !gets the modifiers out of the db
    function get_modifiers () {
         $mods = $this->_adb->GetAll('select * from `'.T_PLUGINS.'` where type="modifier" order by id');
         if($mods){
            $this->modifiers =& $mods;
            $this->assign_by_ref("modifiers",$mods);
         }
         else{
            return false;
         }
    }
    ////
    // !sets the last modified time ( $timestamp is newer )
    // this function takes the modified times of all
    // displayed items and decides if it's modified or not
    // I can't think of many cases where you would use this instead of modifiednow()
    function setmodifytime ($timestamp) {
          if($this->lastmodified < $timestamp && $timestamp <= time()) $this->lastmodified = $timestamp;
          return true;
    }

    ////
    // !modifiednow should be called in responce to a direct user action  changing data
    // resulting in the site being modified, e.g. a new post, an editied post,
    // new link category, new comment etc
    function modified_now() {
          $now = time();
          $query ="update ".T_CONFIG." set value='$now' where name='LAST_MODIFIED'";
          $rs = $this->_adb->Execute($query);
          if($rs !== false && !$rs->EOF){
            $this->setmodifytime($now);
          }
    }

	/*
      All links are generated here.
      This is handy becasue it means we can do any thing with the urls in the future,
      even ones like /computers/my_case_mod.html
      // hmm they should be called _get_*_url not get_*_link !
    */

    ////
    // !Get a link for a category
    /*function _get_section_link(&$id, &$name) {
		
    }*/

    ////
    // !Get a link to the rss file for a category
    /*function _get_section_rss_link(&$sectionid) {
         return 
    }*/

    ////
    // !get a permalink to an entry
    /*function _get_entry_permalink (&$postid) {
         if(defined('CLEANURLS')) return str_replace('%postid%',$postid,URL_POST);
	 else return BLOGURL.'?postid='.$postid;
    }*/

    ////
    // !get a permalink to a single comment
    /*function _get_comment_permalink (&$postid,&$commentid) {
             if(defined('CLEANURLS')) return $this->_get_entry_permalink($postid).'#comment'.$commentid;
	     return BLOGURL.'?postid='.$postid.'#comment'.$commentid;
   }*/
   
   /*function _get_section_id($sectionname) {
 	$sid = $this->sect_by_name[$sectionname];
	if($sid > 0) return $sid;
	else return false;
   }*/

    ////
    // !gets the url to the default rss filr
    /*function _get_rss_url($sectionid=FALSE) {
             // in the future well actuall use $sectionid
             // to return the rss url of just one section
             return BLOGURL.'rss.php';
    }*/
    
    /*function _get_post_trackback_url($postid) {
		return LOQ_APP_URL.'trackback.php/'.$postid.'/';

    }*/

    /*function _get_comment_trackback_url($postid,$commentid) {
               return LOQ_APP_URL.'trackback.php/'.$postid.'/'.$commentid.'/';
    }*/
    function standalone_message($message_title=FALSE,$message=FALSE,$meta_redirect=FALSE, $http_header = FALSE) {
        // THIS FUNCTION WILl KILL THE SCRIPT BEFORE ANYTHING GETS TO THE BROWSER.
        $this->template_dir = LOQ_APP_ROOT.'inc/admin_templates';
        $this->compile_id = 'admin';
        if(!$message) $this->assign('message','No message given!');
	        else $this->assign('message',$message);
    	if(!$message_title) $this->assign('message_title','');
    	        else $this->assign('message_title',$message_title);
    
            $this->assign('meta_redirect',$meta_redirect);
    	ob_end_clean();
        if($http_header) header($http_header);
    	$page = $this->fetch('standalone_message.html');
    	echo $page;
    	die();
    }
    function get_comment_handler($postid){
        return new commentHandler($this->_adb, $postid);
    }

} // end of Loquacity class
?>