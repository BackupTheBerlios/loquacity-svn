<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Eaden McKee, http://www.bblog.com, Tobias Schlottke
 * @copyright &copy; 2003  Eaden McKee <email@eadz.co.nz>, Tobias Schlottke
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
// database, and all the functions it calls are in the $bBlog object.
function identify_admin_archives ()
{
  return array (
    'name'           =>'archives',
    'type'           =>'builtin',
    'nicename'       =>'Archives Admin',
    'description'    =>'Edit archives',
    'authors'         =>'Eaden McKee, Tobias Schlottke',
    'licence'         =>'GPL'
  );
}

$bBlog->assign('form_type','edit');
$bBlog->get_modifiers();

$ph = $bBlog->_ph;
if (isset($_GET['delete']) or isset($_POST['delete'])){
    if ($_POST['confirm'] == "cd".$_POST['delete'] && is_numeric($_POST['delete'])){
        $res = $bBlog->delete_post($_POST['delete']);
        $bBlog->assign('showmessage',TRUE);
        $bBlog->assign('message_title','Message Deleted');
        $bBlog->assign('message_content','The message you selected has now been deleted'); // -1 Redundant  ;)
    }
    else{
        $bBlog->assign('showmessage',TRUE);
        $bBlog->assign('message_title','Are you sure you want to delete it?');
        $bBlog->assign('message_content',"
            <form action='index.php' method='POST'>
            <input type='hidden' name='b' value='archives'>
            <input type='hidden' name='confirm' value='cd".$_POST['delete']."'>
            <input type='hidden' name='delete' value='".$_POST['delete']."'>
            <center><input type='submit' class='bf' name='submit' value='Delete it'></center>
            </form>");
    }
}

if (isset($_POST['edit']) && is_numeric($_POST['edit'])){
    $epost = $ph->get_post($_POST['edit'], true, true);
    $bBlog->assign('title_text',htmlspecialchars($epost['title']));
    $bBlog->assign('body_text',htmlspecialchars($epost['body']));
    $bBlog->assign('selected_modifier',$epost['modifier']);
    $bBlog->assign('editpost',TRUE);
    $bBlog->assign('showarchives','no');
    $bBlog->assign('postid',$_POST['edit']);
    $bBlog->assign('timestampform',timestAmpform($epost['posttime']));

    // to hide a post from the homepage
    if($epost['hidefromhome'] == 1){
        $bBlog->assign('hidefromhomevalue'," checked ");
    }

    // to disable comments either now or in the future
    if($epost['allowcomments'] == 'timed') $bBlog->assign('commentstimedvalue'," checked ");
    elseif($epost['allowcomments'] == 'disallow') $bBlog->assign('commentsdisallowvalue'," checked ");
    else $bBlog->assign('commentsallowvalue'," checked='checked' ");


    if($epost['status'] == 'draft') $bBlog->assign('statusdraft','checked');
    else $bBlog->assign('statuslive','checked');

    $sects = $bBlog->sections;
    $editpostsections = array();
    $_post_sects = (strlen($epost['sections']) > 1) ? explode(":",$epost['sections']) : array($epost['sections']);
    foreach($sects as $id=>$sect){
        if(in_array($id, $_post_sects)){
            $sect['checked'] = true;
            $sects[$id] = $sect;
        }
    }
    $bBlog->assign("sections",$sects);
    //$bBlog->assign_by_ref("sections",$nsects);
}

if ((isset($_POST['postedit'])) && ($_POST['postedit'] == 'true')){
    // a post to be editited has been submitted
    if ((isset($_POST['postedit'])) && (!is_numeric($_POST['postid']))){
        echo "Provided PostID value is not a Post ID. (Fatal error)";
        die;
    }

    if($ph->edit_post($_POST)){
	if ((isset($_POST['send_trackback'])) && ($_POST['send_trackback'] == "TRUE")){
		// send a trackback
		include "includes/trackbackhandler.class.php";
		$tb = new trackbackhandler($bBlog->_adb);
		if (!isset($_POST['title_text']))   { $_POST['title_text']  = ""; }
		if (!isset($_POST['excerpt']))      { $_POST['excerpt']     = ""; }
		if (!isset($_POST['tburl']))        { $_POST['tburl']       = ""; }
		$tb->send_trackback($ph->get_post_permalink($_POST['postid']), $_POST['title_text'], $_POST['excerpt'], $_POST['tburl']);
	}
    }
}

if ((isset($_POST['filter'])) && ($_POST['filter'] == 'true')){
    if ((isset($_POST['shownum'])) && (is_numeric($_POST['shownum'])))
    {
    	$num = $_POST['shownum'];
    }
    else
    {
	$num=20;
    }

	$searchopts['num'] = $num;
	$searchopts['wherestart'] = ' WHERE 1 ';

	if(is_numeric($_POST['showsection']))
	{
		$searchopts['sectionid'] = $_POST['showsection'];
	}

	if($_POST['showmonth'] != 'any')
	{
		$searchopts['month'] = substr($_POST['showmonth'],0,2);
		$searchopts['year']  = substr($_POST['showmonth'],3,4);
	}
	//print_r($searchopts);
	$q = $bBlog->make_post_query($searchopts);
	//echo $q;
	$archives = $bBlog->get_posts($q);
}
if (isset($_POST['allowcomments']) && (is_numeric($_POST['allowcomments']) === true)){
    $sql = 'UPDATE '.T_POSTS.' SET allowcomments=IF(allowcomments="allow", "disallow", "allow") WHERE postid='.intval($_POST['allowcomments']);
    $bBlog->_adb->Execute($sql);
}
else{
	$searchopts['wherestart'] = ' WHERE 1 ';
    $archives = $ph->get_posts($searchopts);
}

$bBlog->assign('postmonths',get_post_months());
$bBlog->assign_by_ref('archives',$archives);
$bBlog->display('archives.html');

function get_post_months()
{
	global $bBlog;
    $rs = $bBlog->_adb->Execute("SELECT FROM_UNIXTIME(posttime,'%Y%m') yyyymm,  posttime from ".T_POSTS." group by yyyymm order by yyyymm");
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
	$day = date('j',$ts);
	$month = date('m',$ts);
	$year = date('Y',$ts);
	$hour = date('H',$ts);
	$minute = date('i',$ts);
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

?>