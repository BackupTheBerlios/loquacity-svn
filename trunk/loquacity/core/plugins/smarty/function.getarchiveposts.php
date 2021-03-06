<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Reverend Jim <jim@revjim.net>
 * @copyright &copy; 2003  Reverend Jim <jim@revjim.net>
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
 
function identify_function_getarchiveposts () {
$help = '
<p>the {getarchiveposts} function is used to retrieve recent posts. It takes the following parameters:<br />
<br />
assign: variable to assign data to<br />
archive: to get ascending sorted results<br />
section: to request recent items in a section<br />
year: year of posts<br />
month: month of posts<br />
day: day of posts<br />
hour: hour of posts<br />
minute: minute of posts<br />
second: second of posts';

  return array (
    'name'           =>'getarchiveposts',
    'type'             =>'function',
    'nicename'     =>'GetArchivetPosts',
    'description'   =>'Retrieves recent blog posts',
    'authors'        =>'Reverend Jim <jim@revjim.net>',
    'licence'         =>'GPL',
    'help'   => $help
  );
}
function smarty_function_getarchiveposts($params, &$loq) {
    $ar = array();
    $opt = array();
	// If "assign" is not set... we'll establish a default.
	if($params['assign'] == '') {
		$params['assign'] = 'posts';
	}
	
	// If "archive" is set, order them ASCENDING by posttime.
	if($params['archive']) {
		$opt['order']=" ORDER BY posttime ";
	}

	if(is_numeric($params['num'])) {
		$opt['num'] = $params['num'];
	}

	if(is_numeric($params['year'])) {
		if(strlen($params['year']) != 4) {
			$loq->trigger_error('getarchiveposts: year parameter requires a 4 digit year');
			return '';
		}
		$opt['year'] = $params['year'];
	}

	if(is_numeric($params['month'])) {
		if(strlen($params['month']) != 2) {
			$loq->trigger_error('getarchiveposts: month parameter requires a 2 digit month');
			return '';
		}
		$opt['month'] = $params['month'];
	}

	if(is_numeric($params['day'])) {
		if(strlen($params['day']) != 2) {
			$loq->trigger_error('getarchiveposts: day parameter requires a 2 digit day');
			return '';
		}
		$opt['day'] = $params['day'];
	}

	if(is_numeric($params['hour'])) {
		if(strlen($params['hour']) != 2) {
			$loq->trigger_error('getarchiveposts: hour parameter requires a 2 digit hour');
			return '';
		}
		$opt['hour'] = $params['hour'];
	}

	if(is_numeric($params['minute'])) {
		if(strlen($params['minute']) != 2) {
			$loq->trigger_error('getarchiveposts: minute parameter requires a 2 digit minute');
			return '';
		}
		$opt['minute'] = $params['minute'];
	}

	if(is_numeric($params['second'])) {
		if(strlen($params['second']) != 2) {
			$loq->trigger_error('getarchiveposts: second parameter requires a 2 digit second');
			return '';
		}
		$opt['second'] = $params['second'];
	}

	if ($params['section'] != '') {
		$opt['sectionid'] = $loq->sect_by_name[$params['section']];
	}

    $posts = $loq->_ph->get_posts($opt, 'html');
        
	// No posts.
    if(!is_array($posts)) {
		return;
	}

	$lastmonth = '';
	$lastdate = '';

	foreach($posts as $key => $value) {
		// It seems silly to do this. Especially since,
		// this kind of check can be done in Smarty template.
		// Additionally, since {newday} and {newmonth} require
		// the data to be in a variable named "post" it may not
		// function at all.
		//
		// We'll leave it here for now.
        
        //print "lastmonth: $lastmonth<br />";
        $month = strftime('%B', $value['posttime']);
        if($month != $lastmonth){
            $posts[$key]['newmonth'] = 'yes';
            //print "month: $month<br />";
        }
        $lastmonth = $month;
        /*if(date('Fy',$posts[$key]['posttime']) != $lastmonth) {
            $posts[$key]['newmonth'] = 'yes';
        }
        $lastmonth = date('Fy',$posts[$key]['posttime']);
        if(date('Ymd',$posts[$key]['posttime']) != $lastdate) {
            $posts[$key]['newday'] = 'yes';
        }
        $lastdate = date('Ymd',$posts[$key]['posttime']);*/
	}
    //var_dump($posts);
	$loq->assign($params['assign'],$posts);

    return;
}

?>
