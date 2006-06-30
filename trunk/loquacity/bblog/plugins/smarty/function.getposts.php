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
    <p>For more detailed help, see the bBlog template manual</p>
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

function smarty_function_getposts($params, &$bBlog) {
    $ar = array();
    $opt = array();
    $ph = $bBlog->_ph;
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
        $bBlog->assign($params['assign'],get_single_post($bBlog, $ph, $postid));
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
		  $opt['sectionid'] = $bBlog->sect_by_name[$params['section']];
	}
	
	if($bBlog->show_section) {
		$opt['sectionid'] = $bBlog->show_section;
	}

	if(is_numeric($params['year'])) {
		if(strlen($params['year']) != 4) {
			$bBlog->trigger_error('getposts: year parameter requires a 4 digit year');
			return;
		}
		$opt['year'] = $params['year'];
	}

    foreach(array('month', 'day', 'hour', 'minute', 'second') as $type){
        if(is_numeric($params[$type])) {
            if(strlen($params[$type]) != 2) {
                $bBlog->trigger_error('getposts: '.$type.' parameter requires a 2 digit month');
                return;
            }
            $opt[$type] = $params[$type];
        }
    }

	$opt['home'] = $params['home'];

    $ar['posts'] = $ph->get_posts($opt);    
	// No posts.
    if(!is_array($ar['posts'])) {
	   return;
    }

    $lastmonth = 0;
    $lastdate = 0;

    foreach($ar['posts'] as $key => $value) {
        /* check if new day  - used by block.newday.php */
        if(date('Ymd',$ar['posts'][$key]['posttime']) != $lastdate) {
          $ar['posts'][$key]['newday'] = TRUE;
        }
        $lastdate = date('Ymd',$ar['posts'][$key]['posttime']);
    
        /* check if new month - use by block.newmonth.php */
        if(date('Fy',$ar['posts'][$key]['posttime']) != $lastmonth) {
          $ar['posts'][$key]['newmonth'] = TRUE;
        }
        $lastmonth = date('Fy',$ar['posts'][$key]['posttime']);
    }
    $posts = apply_modifier($bBlog, $ar['posts']);
    $bBlog->assign($params['assign'],$posts);

    return;
	
}

function get_single_post(&$bBlog, &$ph, $postid){
    $post = $ph->get_post($postid);
    if(!is_array($post))
        return false;
    $post = apply_modifier($bBlog, array($post));
    $post = $post[0];
    $post['newday'] = 'yes';
    $post['newmonth'] = 'yes';
    
    return $post;
}

/**
 * Loads and applies a Smarty based modifier to a post
 * 
 * @param array $posts
 * @return array
 */
function apply_modifier(&$bBlog, $posts){
    if(is_array($posts)){
        foreach($posts as $key => $post){
            if(array_key_exists('modifier', $post)){
                require_once( $bBlog->_get_plugin_filepath('modifier', $post['modifier']));
                $mod_func = 'smarty_modifier_'.$post['modifier'];
                $posts[$key]['body'] = $mod_func($post['body']);
                $posts[$key]['applied_modifier'] = $post['modifier'];
            }
        }
    }
    return $posts;
}
?>