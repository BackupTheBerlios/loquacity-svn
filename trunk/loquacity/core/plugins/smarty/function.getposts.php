<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Eaden McKee - http://www.bblog.com, Reverend Jim
 * @copyright &copy; 2003  Eaden McKee <email@eadz.co.nz>, Reverend Jim <jim@revjim.net>
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
function identify_function_getposts () {
    $help = '
    <p>the {getposts} function is used to retrieve a blog post or posts. <br />
    It takes the following parameters:<br />
    <br />
    <ul>
    <li>assign: variable to assign data to ( defaults to "posts", or "post" if postid is given) e.g. assign="posts"</li>
    <li>postid: to get one post e.g. postid=1</li>
    <li>archive: to get ascending sorted results ( older posts first )</li>
    <li>num: for number of entries to return, e.g. num=10</li>
    <li>section: to request recent items in a section, e.g. section="news"</li>
    <li>sectionid: to request recent items in a section, by specifing the sectionid instead of the name</li>
    <li>skip: number of entries to skip, e.g. {getposts section=news num=10 skip=10} will return 10 posts, but not the first ten but the second ten. for use with paging</li>
    <li>home : if this is set with home=true, posts that have the "do not show on homepage" option set will not show</li>
    <li>year: year of posts, e.g. year=2003 will only show posts for year 2003</li>
    <li>month: month of posts</li>
    <li>day: day of posts</li>
    <li>hour: hour of posts</li>
    <li>minute: minute of posts</li>
    <li>second: second of posts</li>
    </ul>
    <p>For more detailed help, see the Loquacity template manual</p>
    ';
    
      return array (
        'name'           =>'getposts',
        'type'             =>'function',
        'nicename'     =>'Get Posts',
        'description'   =>'Retrieves blog posts',
        'authors'        =>'Eaden McKee, Reverend Jim',
        'licence'         =>'GPL',
        'help'   => $help
      );
}

function smarty_function_getposts($params, &$loq) {
    $ar = array();
    $opt = array();
    $ph = $loq->_ph;
    if(is_numeric($params['postid']) && $params['postid'] > 0) {
	   $postid = $params['postid'];
	}
    else {
	   $postid = FALSE;
	}
	// If "assign" is not set... we'll establish a default.
	if($params['assign'] == '') {
        if($postid)
            $params['assign'] = 'post';
		else
            $params['assign'] = 'posts';
	}

    if($postid && is_int($postid)){
        $loq->assign($params['assign'], get_single_post($loq, $ph, $postid));
        return;
    }

	// If "archive" is set, order them ASCENDING by posttime.
	if($params['archive']) {
		$opt['order']=" ORDER BY posttime ";
	}
	// If num is set, we'll only get that many results in return
	if(is_numeric($params['num'])) {
		$opt['num'] = $params['num'];
	}
	// If skip is set, we'll skip that many results
	if(is_numeric($params['skip'])) {
		$opt['skip'] = $params['skip'];
	}
	if ($params['section'] != '') {
		  $opt['sectionid'] = $loq->sect_by_name[$params['section']];
	}
	
	if($loq->show_section) {
		$opt['sectionid'] = $loq->show_section;
	}

	if(is_numeric($params['year'])) {
		if(strlen($params['year']) != 4) {
			$loq->trigger_error('getposts: year parameter requires a 4 digit year');
			return;
		}
		$opt['year'] = $params['year'];
	}

    foreach(array('month', 'day', 'hour', 'minute', 'second') as $type){
        if(is_numeric($params[$type])) {
            if(strlen($params[$type]) != 2) {
                $loq->trigger_error('getposts: '.$type.' parameter requires a 2 digit month');
                return;
            }
            $opt[$type] = $params[$type];
        }
    }

	$opt['home'] = $params['home'];

    if(($posts = $ph->get_posts($opt, 'html')) !== false){
        $lastmonth = 0;
        $lastdate = 0;
        foreach($posts as $key => $value) {
            if(date('Ymd',intval($posts[$key]['posttime'])) != $lastdate) {
              $posts[$key]['newday'] = TRUE;
            }
            $lastdate = intval(date('Ymd',$posts[$key]['posttime']));
            if(intval(date('Fy',$posts[$key]['posttime'])) != $lastmonth) {
              $posts[$key]['newmonth'] = TRUE;
            }
            $lastmonth = strftime('%B%Y', intval($posts[$key]['posttime'])); //date('Fy',$ar['posts'][$key]['posttime']);
        }
        $loq->assign($params['assign'],$posts);
    }
    else{
        $loq->assign($params['assign'],array(0 => array('title' => $ph->status)));
    }
}

function get_single_post(&$loq, &$ph, $postid){
    $post = $ph->get_post($postid, false, 'html');
    if(!is_array($post))
        return false;
    $post['newday'] = 'yes';
    $post['newmonth'] = 'yes';
    return $post;
}
?>