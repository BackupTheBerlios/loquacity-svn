<?php
/*

   '||     '||'''|,  '||`
    ||      ||   ||   ||
    ||''|,  ||;;;;    ||  .|''|, .|''|,
    ||  ||  ||   ||   ||  ||  || ||  ||
   .||..|' .||...|'  .||. `|..|' `|..||
                                     ||
               v0.7.5             `..|'

** bBlog Weblog http://www.bblog.com/
** Copyright (C) 2005  Eaden McKee <email@eadz.co.nz>
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

if (!defined("OBJECT"))     define("OBJECT","OBJECT",true);
if (!defined("ARRAY_A"))    define("ARRAY_A","ARRAY_A",true);
if (!defined("ARRAY_N"))    define("ARRAY_N","ARRAY_N",true);

class bBlog extends Smarty {

	var $template;
	var $num_homepage_entries = 20;
	var $templatepage = "index.html";
    // for comments
	var $highestlevel = 0;
	var $com_order_array = array();
	var $com_finalar;
    ////
    // !bBlog constructor function
	function bBlog() {
        // initilize smarty by calling the smarty constructor class
        parent::Smarty();
        // connect to database
        $this->_adb = NewADOConnection('mysql://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.'/'.DB_DATABASE.'?persist');
        
	 	$this->db = new db(DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_HOST);
        $this->num_rows =& $this->db->num_rows;
        $this->insert_id =& $this->db->insert_id;
        
        //Load the config
        $config =& new configHandler($this->db);
        $config->loadConfig();
        $this->assign('blogname',C_BLOGNAME);
        $this->assign('blogdescription',C_BLOG_DESCRIPTION);
        $this->assign('blogurl',BLOGURL);
	  	$this->assign('bblogurl',BBLOGURL);
	  	$this->assign('metakeywords',C_META_KEYWORDS);
	  	$this->assign('metadescription',C_META_DESCRIPTION);
		$this->assign('charset',C_CHARSET);
		$this->assign('direction', C_DIRECTION);
 
        // initial time from config table, based on last updated stuff.
        // this is just the initial value.
        //$this->lastmodified = C_LAST_MODIFIED;
        //$this->register_postfilter("update_when_compiled");
        // load up the sections
        $this->get_sections();

        //start the session that we need so much ;)
        if(!session_id()) {         
	  	    session_start();
	    }
        $this->_ph =& new postHandler(&$this->db);
	} // end of function bBlog

    // database stuff
   function query($query) { return $this->db->query($query); }
   function get_results ($query,$output=OBJECT) { return $this->db->get_results($query,$output); }
   function get_row ($query,$output=OBJECT,$y=0) { return $this->db->get_row($query,$output,$y); }
   function get_var ($query,$x=0,$y=0) { return $this->db->get_var($query,$x,$y); }

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
		$archs = $this->get_results($stmt);

		if($this->num_rows <= 0) {
			return false;
		}

		$ret = array();

		foreach($archs as $arch) {
				$year = substr($arch->archname,0,4);
				$month = substr($arch->archname,4,2);
				$day = substr($arch->archname,6,2);
				$hour = substr($arch->archname,8,2);
				$minute = substr($arch->archname,10,2);
				$second = substr($arch->archname,12,2);
				$ts = mktime(
					$hour ? $hour : 0, 
					$minute ? $minute : 0,
					$second ? $second : 0,
					$month ? $month : 1,
					$day ? $day : 1,
					$year ? $year : 1970);
				$ret[] = array(
					'archname' => $arch->archname,
					'year' => $year,
					'month' => $month,
					'day' => $day,
					'hour' => $hour,
					'minute' => $minute,
					'second' => $second,
					'ts' => $ts,
					'count' => $arch->cnt
				);
		}

		return $ret;
	}

	
    ////
	// !check against the user and pass stored in the bB authors table
        function userauth($user, $pass, $setcookie = FALSE) {
            $query = "SELECT `id` FROM `".T_AUTHORS."` WHERE `nickname`='".my_addslashes($user)."' AND `password`='".my_addslashes($pass)."'";
            if ($user_id = $this->get_var($query)) {
                $_SESSION['user_id'] = $user_id;
                return $user_id;
            } else {
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
        // we use a relitive path because otherwise we need
        // as many compile directories as template
        // and to make things easy for users we don't want them
        // to have to chmod 777 too many directories.
		parent::display($page);
        $o = ob_get_contents();
        ob_end_clean();
        /* this doesn't work properly yet as the page stays cached in the browser even when things change */
        /* so we'll make it always fresh until this is worked through.
        if(!defined('IN_BBLOG_ADMIN')) {
           $lmdate = gmdate('D, d M Y H:i:s \G\M\T',$this->lastmodified);
           header('Last-Modified: '.$lmdate);
           if ($_SERVER["HTTP_IF_MODIFIED_SINCE"] == $lmdate){
   		 	  header("HTTP/1.1 304 Not Modified");
               exit;
		   }
        } else { // we want the page always to be fresh :)
              // borrowed from wordpress :
               @header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 				// Date in the past
               @header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
               @header("Cache-Control: no-store, no-cache, must-revalidate"); 	// HTTP/1.1
               @header("Cache-Control: post-check=0, pre-check=0", false);
               @header("Pragma: no-cache"); 									// HTTP/1.0

          } */
          echo $o;
          if($addfooter)
            echo buildfoot();
	}

	
    ////
    // !called once to load up the sections
    // and assign them to $sections in the template
	function get_sections () {

         $sects = $this->get_results("select * from ".T_SECTIONS." order by name");
         if($this->num_rows > 0) {
              $nsects = array();
              foreach($sects as $sect) {
                    // we'll make an array just like the one from the database
                    // but with URL's
                    // make some useful lookup tables
                    $this->sect_by_id[$sect->sectionid] = $sect->name;
                    $this->sect_by_name[$sect->name] = $sect->sectionid;
                    $this->sect_nicename[$sect->sectionid] = $sect->nicename;
                    $this->sect_url[$sect->sectionid] = $this->_get_section_link($sect->sectionid);
                    $this->sect_rss_url[$sect->sectionid] = $this->_get_section_rss_link($sect->sectionid);
                    $nsect = $sect;
                    $nsect->url = $this->_get_section_link($sect->sectionid);
                    $nsect->rss_url = $this->_get_section_rss_link($sect->sectionid);
                    $nsects[] = $nsect;
              }
              // now the section array is available in any template
              $this->assign_by_ref('sections',$nsects);
              // we use $this->sections array a lot.
              $this->sections =& $nsects;
              return  $nsects;
         } else return FALSE;
	}
	
    ////
    // !gets the modifiers out of the db
    function get_modifiers () {
             $mods = $this->get_results('select * from '.T_PLUGINS.' where type="modifier" order by id');
             $this->modifiers =& $mods;
             $this->assign_by_ref("modifiers",$mods);
             return $mods;
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
          $this->query("update ".T_CONFIG." set value='$now' where name='LAST_MODIFIED'");
          $this->setmodifytime($now);
    }

	/*
      All links are generated here.
      This is handy becasue it means we can do any thing with the urls in the future,
      even ones like /computers/my_case_mod.html
      // hmm they should be called _get_*_url not get_*_link !
    */

    ////
    // !Get a link for a category
    function _get_section_link(&$sectionid) {
	    	$sectionname = $this->sect_by_id[$sectionid];
		if(defined('CLEANURLS')) return str_replace('%sectionname%',$sectionname,URL_SECTION);
		return BLOGURL.'?sectionid='.$sectionid;
    }

    ////
    // !Get a link to the rss file for a category
    function _get_section_rss_link(&$sectionid) {
         return BLOGURL.'rss.php?sectionid='.$sectionid;
    }

    ////
    // !get a permalink to an entry
    function _get_entry_permalink (&$postid) {
         if(defined('CLEANURLS')) return str_replace('%postid%',$postid,URL_POST);
	 else return BLOGURL.'?postid='.$postid;
    }

    ////
    // !get a permalink to a single comment
    function _get_comment_permalink (&$postid,&$commentid) {
             if(defined('CLEANURLS')) return $this->_get_entry_permalink($postid).'#comment'.$commentid;
	     return BLOGURL.'?postid='.$postid.'#comment'.$commentid;
   }
   
   function _get_section_id($sectionname) {
 	$sid = $this->sect_by_name[$sectionname];
	if($sid > 0) return $sid;
	else return false;
   }

    ////
    // !gets the url to the default rss filr
    function _get_rss_url($sectionid=FALSE) {
             // in the future well actuall use $sectionid
             // to return the rss url of just one section
             return BLOGURL.'rss.php';
    }
    
    function _get_post_trackback_url($postid) {
		return BBLOGURL.'trackback.php/'.$postid.'/';

    }

    function _get_comment_trackback_url($postid,$commentid) {
               return BBLOGURL.'trackback.php/'.$postid.'/'.$commentid.'/';
    }
    function standalone_message($message_title=FALSE,$message=FALSE,$meta_redirect=FALSE, $http_header = FALSE) {
            // THIS FUNCTION WILl KILL THE SCRIPT BEFORE ANYTHING GETS TO THE BROWSER.
            $this->template_dir = BBLOGROOT.'inc/admin_templates';
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
        return new commentHandler(&$this->_adb, $postid);
    }

} // end of bBlog class
?>
