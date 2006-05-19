<?php

class postHandler {

    function postHandler(&$db) {
        $this->_db = $db;
    }
    ////
    // !inserts a new entry
    // returns the new entryid on success
    // error message on fail
    // assumes that my_addslashes() has already been applied and data is safe.
    function new_post($post) {
        //$this->modifiednow();
        $now = time();
        $section = '';
        if(sizeof($post->sections)>0) {
            $sections = implode(":",$post->sections);
            // We add an extra ":" at the begging and end
            // of this string to ensure that we can locate
            // the sections properly.
            $section_q = " sections =':$sections:', ";
        }
        if (!isset($post->ownerid)) {
            $post->ownerid = $_SESSION['user_id'];
        }

        $hidefromhome_q = ($post->hidefromhome == 'hide') ? " hidefromhome='1', " : " hidefromhome='0', ";

        if($post->allowcomments == ('allow' or 'disallow' or 'timed'))
            $allowcomments_q = " allowcomments='{$post->allowcomments}', ";

        if(is_numeric($post->autodisabledate))
            $autodisable_q = " autodisabledate='{$post->autodisabledate}', ";

        $q_insert = "INSERT INTO ".T_POSTS." SET
            title       ='$post->title',
            body        ='$post->body',
            posttime    ='$now',
            modifytime  ='$now',
            status      ='$post->status',
            $section_q
            $hidefromhome_q
            $allowcomments_q
            $autodisable_q
            modifier    ='$post->modifier',
            ownerid    ='$post->ownerid'";
            
            $this->_db->query($q_insert);
            $postid = $this->_db->insert_id;
        if($postid > 0)
            return $postid;
        else
            return false;
        
    }
    /**********************************************************************
    ** get_entries
    ** Gets blog entries from the db
    ** array, $limit ex. " LIMIT 0,20 ", $order ex. " ORDER BY tstamp desc "
    ** $sectionid ex = 1
    ** Return
    **********************************************************************/
    ////
    // !Gets blog entries from the db from a query.
    // if apply mods is true, it will apply the modifiers
    /**
     * Retrieve one or most posts based upon criteria
     * 
     * @param array $crit Hash of selection criteria
     * @param bool $raw If false return in a format sufficient for browser display
     * @return array
     */
    function get_posts($crit, $raw=FALSE) {
        if(is_array($crit)){
            $posts = $this->_db->get_results($this->make_post_query($crit));
            if($this->_db->num_rows > 0){
                if($raw)
                    return $posts;
                else {
                    //load required plugins/modifiers
                    //Defer to pre_post stage
                    /*foreach($posts as $post) {
                          // this looks a bit wacky, but i think it works well..
                          $modifiers[$post->modifier] = $post->modifier;
                    }
                    if(sizeof($modifiers) > 0) {
                          foreach ($modifiers as $modifier) {
                             require_once $this->_get_plugin_filepath('modifier',$modifier);
                          }
                    }*/
                    $finalposts = array();
                    foreach ($posts as $post) {
                             $finalposts[] = $this->prep_post($post);
                    }
                    return $finalposts;
                }
            }
            else
                return array(array("title"=>"No posts found"));
        }
        else{
            return array(array("title"=>"No selection cirteria supplied"));
        }
        #$posts = $this->_db->get_results($q); // $posts returned as an object
    }

    ////
    // !formats a single post into a useful array suitable for smarty
    // i.e. an associatve array not an object
    // this function is pretty basic at the moment, but all
    // sorts of things will happen in the future.
    // it assumes that the required plugin modifiers have been loaded
    function prep_post(&$post) {
        //first do the basics

        $npost['id'] = $post->postid;
        $npost['postid'] = $post->postid;
        $npost['permalink'] = (defined('CLEANURLS')) ? str_replace('%postid%',$post->postid,URL_POST) : BLOGURL.'?postid='.$post->postid;
        $npost['trackbackurl'] = BBLOGURL.'trackback.php/'.$post->postid.'/';
        $npost['title'] = $post->title;
         
        // do the body text
        /*if($post->modifier != '') {
             // apply a smarty modifier to the body
             // in the future we could have multi modifiers
             // but I decided agains that for now, you can always make a
             // modifier that calls other modifiers if you really want to .
             $mod_func = 'smarty_modifier_'.$post->modifier;
             $npost['body'] = $mod_func($post->body);
             $npost['applied_modifier'] = $post->modifier;
        }
        else {*/
        if($post->modifier !== '')
            $npost['modifier'] = $post->modifier;
         $npost['body'] = $post->body;
             #$npost['applied_modifier'] = 'none';
        //}
        if(USE_SMARTY_TAGS_IN_POST == TRUE) {
            $this->assign('smartied_post', $npost['body']);
            $tmptemplatedir = $this->template_dir;
            $tmpcompileid = $this->compile_id;
            $this->template_dir = BBLOGROOT.'inc/admin_templates';
            $this->compile_id = 'internal';
            $npost['body'] = $this->fetch('smartypost.html');
            $this->template_dir = $tmptemplatedir;
            $this->compile_id = $tmpcompileid;
        }
        $npost['status'] = $post->status;

        // in the future
        $npost['posttime'] = $post->posttime;
        $npost['modifytime'] = $post->modifytime;

        // what we need here is that the date format
        // is available in the control panel as an option
        // this is only here as a convience, the date_format modifier should be used.
        $npost['posttime_f'] = date("D M j G:i:s T Y",$post->posttime);
        $npost['modifytime_f'] = date("D M j G:i:s T Y",$post->modifytime);
        $npost['sections']   = array();
        switch($post->commentcount) {
             case 1 : $npost['commenttext'] = "One comment"; break;
             case 0 : $npost['commenttext'] = "Comment"; break;
             default: $npost['commenttext'] = $post->commentcount." comments"; break;
        }
        $npost['commentcount'] = $post->commentcount;
        if($post->sections != '') {
              // we are assuming that there is at least one section
              // becasue you shouldnt' have ":" or something in there !
              $tmp_sec_ar = explode(":",$post->sections);
              foreach ($tmp_sec_ar as $tmp_sec) {
              // Make sure it isn't the empty section at
              // the beginning and end of each section list.
                  if($tmp_sec != '') {
                  // Populate Sections Array
                          $npost['sections'][] = array(
                                            "id"=>$tmp_sec,
                                            "name"=>$this->sect_by_id[$tmp_sec],
                                            "nicename"=>$this->sect_nicename[$tmp_sec],
                                            "url"=>$this->sect_url[$tmp_sec]);
                  }
              }
        }
        //add the author info
        $npost['author'] = array(
             'id' => $post->ownerid,
             'nickname' => $post->nickname,
             'email' => $post->email,
             'fullname' => $post->fullname);
        $npost['hidefromhome'] = $post->hidefromhome;
        $npost['autodisabledate'] = $post->autodisabledate;
        if($post->allowcomments == 'disallow' or ($post->allowcomments == 'timed' and $post->autodisabledate < time())){
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
        $order          = " ORDER BY posttime desc ";
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
        if (isset($second))                                 $where .= " AND FROM_UNIXTIME(posttime,'%S') = '" . addslashes($second) . "' ";
         
        // There should be a ":" at the beginning and end of 
        // any sections list
        if ((isset($sectionid)) && ($sectionid != FALSE))   $where .= " AND sections like '%:$sectionid:%' ";

        if($home) $where .= " AND hidefromhome='0' ";
        $q = "SELECT posts.$what, authors.nickname, authors.email, authors.fullname FROM ".T_POSTS." AS posts LEFT JOIN ".T_AUTHORS." AS authors ON posts.ownerid = authors.id $wherestart $where $order $limit ";
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
        // this makes it safe for general use.
        // we don't want ppl being able to view drafts.
        if (!$draftok)
            $draft_q = "AND posts.status='live' ";
        else
            $draft_q = '';
            
        // php doesnt have an unless function :
        // unless(is_numeric($postid)) return false
        // so OR does the trick :) ( and it's cleaner. )
        if (!is_numeric($postid))
            return false;
            
        $q = "SELECT posts.*, authors.nickname, authors.email, authors.fullname FROM ".T_POSTS." AS posts LEFT JOIN ".T_AUTHORS." AS authors ON posts.ownerid = authors.id WHERE posts.postid='$postid' $draft_q LIMIT 0,1";
        $post = $this->_db->get_row($q);
        if(isset($post)){
            if ($raw)
                return $post;
            else{
                #require_once $this->_get_plugin_filepath('modifier', $post->modifier);
                return $this->prep_post($post);
            }
        }
            else return FALSE;
    }   
    
    ////
    // !deletes a post
    function delete_post($postid) {
        if(!is_numeric($postid)) return false;
        $this->modifiednow();
        // delete comments
        $q1 = "DELETE FROM ".T_COMMENTS." WHERE postid='$postid'";
        $this->_db->query($q1);
        // delete post
        $q2 = "DELETE FROM ".T_POSTS." WHERE postid='$postid'";
        $this->_db->query($q2);
        if($this->rows_affected  == 1)
            return true;
        else 
            return false;
    }
    
    ////
    // !edits a post
    function edit_post($params) {
        $now = time();
        if(!is_numeric($params['postid'])) return false;
        
        $q = 'UPDATE '.T_POSTS.' SET title="'.$params['title'].'", body="'.$params['body'].'" ';
        $q .= ", modifytime='$now'";
        if($params['sections']) {
            // We place a ":" at the beginning and end of the sections
            // string to ensure that we can locate the sections
            // properly.
            $q .= ', sections=";'.$params['sections'].':"';
        }
        elseif ($params['edit_sections']) {
            $q .=", sections='' ";
        }
        if($params['hidefromhome'] == 'hide') $q .= ", hidefromhome='1'";
        if($params['hidefromhome'] == 'donthide') $q .= ", hidefromhome='0'";

        if($params['allowcomments'] == ('allow' or 'disallow' or 'timed' )){
            $q .= ', allowcomments="'.$params['allowcomments'].'"';
            if($params['allowcomments'] == 'timed' && is_numeric($params['autodisabledate']))
                $q .= ', autodisabledate="'.$params['autodisabledate'].'"';
        }
        if($params['status'])   $q .= ', status="'.$params['status'].'"';
        if($params['modifier']) $q .= ',modifier="'.$params['modifier'].'"';
        if($params['timestamp']) $q .= ',posttime="'.$params['timestamp'].'"';
        $q .=' WHERE postid='.$params['postid'];

        $res = $this->_db->query($q);
        return true;
    }
}
?>