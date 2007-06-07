<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Kenneth Power <telcor@users.berlios.de>, Eaden McKee, http://www.bblog.com, Tobias Schlottke
 * @copyright @copy; 2006, 2007 Kenneth Power <telcor@users.berlios.de>, &copy; 2003  Eaden McKee <email@eadz.co.nz>, Tobias Schlottke
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
 * handles showing a list of entries to edit/delete
 */

// now it may be an idea to do a if(!defined('IN_BBLOG')) die "hacking attempt" type thing but
// i'm not sure it's needed, as without this file being included it hasn't connected to the
// database, and all the functions it calls are in the $loq object.
function identify_admin_archives ()
{
  return array (
    'name'           =>'archives',
    'type'           =>'builtin',
    'nicename'       =>'Archives Admin',
    'description'    =>'Edit archives',
    'authors'         =>'Eaden McKee, Tobias Schlottke',
    'licence'         =>'GPL',
    'help'			 => ''
  );
}

$loq->assign('form_type','edit');
$loq->get_modifiers();
$actions = array('delete', 'edit', 'postedit', 'filter', 'allowcomments');

if(in_array($_POST['action'], $actions)){
	$postid = intval($_POST['postid']);
	switch($_POST['action']){
		case 'delete':
			deletePost($loq, $postid);
			break;
		case 'edit':
			editPost($loq, $postid);
			break;
		case 'postedit':
			savePost($loq, $postid);
			break;
		case 'filter':
			filterDisplay($loq);
			break;
		case 'allowcomments':
			allowComments($loq, $postid);
			break;
		default:
			//Unknown - handle this error
			break;
	}
}
else{
	defaultDisplay($loq);
}

function deletePost(&$loq, $postid=null){
	if(is_null($postid) || $postid === 0){
		return;
	}
    if (isset($_POST['confirm']) && $_POST['confirm'] == "cd".$postid){
        $res = $loq->_ph->delete_post($postid);
        $loq->assign('showmessage',TRUE);
        $loq->assign('message_title','Message Deleted');
        $loq->assign('message_content','The message you selected has now been deleted'); // -1 Redundant  ;)
		defaultDisplay($loq);
    }
    if(isset($_POST['submit']) && ($_POST['submit'] === 'Delete it') && !isset($_POST['confirm'])){
    	defaultDisplay($loq);
    }
    else{
        $loq->assign('showmessage',TRUE);
        $p = $loq->_ph->get_post($postid, true);
        $loq->assign('message_title','Confirm Delete');
        $loq->assign('message_content','
            <form action="index.php" method="POST">
            <input type="hidden" name="b" value="archives" />
			<input type="hidden" name="action" value="delete" />
            <input type="hidden" name="postid" value="'.$postid.'" />
			<input type="checkbox" name="confirm" value="cd'.$postid.'" /> Yes, I really want to delete the post "'.htmlspecialchars($p['title']).'"
            <center><input type="submit" class="bf" name="submit" value="Delete it" /></center>
            </form>');
        $loq->assign('showarchives','no');
    	defaultDisplay();
    }
}
function editPost(&$loq, $postid=null){
	if(is_null($postid) || $postid === 0){
		return;
	}
	
    $epost = $loq->_ph->get_post($postid, true);
    $loq->assign('title_text',htmlspecialchars($epost['title']));
    $loq->assign('body_text',htmlspecialchars($epost['body']));
    $loq->assign('selected_modifier',$epost['modifier']);
    $loq->assign('editpost',TRUE);
    $loq->assign('showarchives','no');
    $loq->assign('postid',$postid);
    $loq->assign('timestampform',timestampform($epost['posttime']));

    // to hide a post from the homepage
    if($epost['hidefromhome'] == 1){
        $loq->assign('hidefromhomevalue'," checked ");
    }

    // to disable comments either now or in the future
    if($epost['allowcomments'] == 'timed'){
        $loq->assign('commentstimedvalue',"checked");
    }
    elseif($epost['allowcomments'] == 'disallow'){
        $loq->assign('commentsdisallowvalue',"checked");
    }
    else{
        $loq->assign('commentsallowvalue',"checked");
    }
	$sects = $loq->sections;
    $editpostsections = array();
    $_post_sects = (strlen($epost['sections']) > 1) ? explode(":",$epost['sections']) : array($epost['sections']);
    foreach($sects as $id=>$sect){
        if(in_array($id, $_post_sects)){
            $sect['checked'] = true;
            $sects[$id] = $sect;
        }
    }
    $loq->assign("sections",$sects);

    if($epost['status'] == 'draft'){
    	$loq->assign('statusdraft','checked');
    }
    else{
    	$loq->assign('statuslive','checked');
    }
    defaultDisplay($loq);
}

function savePost(&$loq, $postid=null){
	if(is_null($postid) || $postid === 0){
		return;
	}
    // a post to be edited has been submitted
    if ((isset($_POST['postedit'])) && (!is_numeric($postid))){
        echo "Provided PostID value is not a Post ID. (Fatal error)";
        die;
    }

    if($loq->_ph->edit_post($_POST)){
		if ((isset($_POST['send_trackback'])) && ($_POST['send_trackback'] == "TRUE")){
			// send a trackback
			include "includes/trackbackhandler.class.php";
			$tb = new trackbackhandler($loq->_adb);
			if (!isset($_POST['title_text']))   { $_POST['title_text']  = ""; }
			if (!isset($_POST['excerpt']))      { $_POST['excerpt']     = ""; }
			if (!isset($_POST['tburl']))        { $_POST['tburl']       = ""; }
			$tb->send_trackback($ph->get_post_permalink($_POST['postid']), $_POST['title_text'], $_POST['excerpt'], $_POST['tburl']);
		}
    }
	defaultDisplay($loq);
}

function filterDisplay(&$loq){
    if ((isset($_POST['shownum'])) && (is_numeric($_POST['shownum']))){
    	$searchopts['num'] = intval($_POST['shownum']);
    }
    else{
		$searchopts['num']=20;
    }

	if(is_numeric($_POST['showsection'])){
		$searchopts['sectionid'] = intval($_POST['showsection']);
	}

	if($_POST['showmonth'] != 'any'){
		list($searchopts['month'], $searchopts['year']) = explode('-', $_POST['showmonth']);
	}
	
	$archives = $loq->_ph->get_posts($searchopts);
	defaultDisplay($loq, $archives);
}

function allowComments(&$loq, $postid=null){
	if(is_null($postid) || $postid === 0){
		return;
	}
    $sql = 'UPDATE '.T_POSTS.' SET allowcomments=IF(allowcomments="allow", "disallow", "allow") WHERE postid='.intval($postid);
    $loq->_adb->Execute($sql);
	defaultDisplay($loq);
}

function get_post_months(){
	global $loq;
    $rs = $loq->_adb->Execute("SELECT FROM_UNIXTIME(posttime,'%Y%m') yyyymm,  posttime from `".T_POSTS."` group by yyyymm order by yyyymm");
    if($rs !== false && !$rs->EOF){
        $months = array();
        while($month = $rs->FetchRow()){
            $nmonth['desc'] = date('F Y',$month['posttime']);
            $nmonth['numeric'] = date('m-Y',$month['posttime']);
            $months[]  = $nmonth;
        }
        return $months;
    }
    else{
        return false;
    }
}

function timestampform($ts){
	$day = strftime('%e',$ts);
	$month = strftime('%m',$ts);
	$year = strftime('%G',$ts);
	$hour = strftime('%H',$ts);
	$minute = strftime('%M',$ts);
	$o  = "<span class='ts'>Day</span> /
	       <span class='ts'>Month</span> /
	       <span class='ts'>Year</span> @
	       <span class='ts'>24hours</span> :
	       <span class='ts'>Minutes</span><br />
	       <input type='text' name='ts_day' value='$day' class='ts' size='5'/> /
	       <input type='text' name='ts_month' value='$month' class='ts' size='5'/> /
	       <input type='text' name='ts_year' value='$year' class='ts' size='7'/> @
           <input type='text' name='ts_hour' value='$hour' class='ts' size='5'/> :
           <input type='text' name='ts_minute' value='$minute' class='ts' size='5'/>
           ";
	return $o;
}

function maketimestamp($day,$month,$year,$hour,$minute){
    return mktime($hour, $minute, 00, $month, $day, $year);
}

function defaultDisplay(&$loq, $archives=null){
	$searchopts = array();
	if(is_null($archives)){
		$archives = $loq->_ph->get_posts($searchopts, true, 'html');
	}
	
	$loq->assign('postmonths',get_post_months());
	$loq->assign_by_ref('archives',$archives);
	$loq->display('archives.html');
}
?>