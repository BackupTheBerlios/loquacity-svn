<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Tanel Raja
 * @copyright &copy; 2003  Tanel Raja
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
 *   function.calendar.php - calendar plugin
 */
function identify_function_calendar () {

    $help = '
	<p>
	This plugin displays the calendar module. It uses calendar.html as a template.
	it has two parameter: week_start, which allows you two choose what day is the
	first day a week (default is 1 (Monday). Sunday is 0, Monday is 1, ...) and 
	locale, which can be used to force the script to another locale (ie de_DE for 
	German, etc.). Default locale is whatever your server has been set to use as 
	default.
	</P>';

     return array (
    	'name'		=> 'calendar',
    	'type'		=> 'function',
    	'nicename'	=> 'Calendar',
    	'description'	=> 'Makes a calendar of the current month',
    	'authors'	=> 'Tanel Raja',
    	'licence'	=> 'GPL',
    	'help'		=> $help
    );
    
}

function smarty_function_calendar($params, &$bBlog) {
    
    $date = getdate();
    
    $today = $date["mday"];
    $month = $date["mon"];
    $year = $date["year"];
    
    $new_month = (isset($_GET["month"])) ? $_GET['month'] : false;
    $new_year = (isset($_GET["year"])) ? $_GET['year'] : false;

    if ($new_month && $new_year) {
        $date = getdate(mktime(0, 0, 0, $new_month, 1, $new_year));
        $show_month = $date["mon"];
        $show_year = $date["year"];
    }
    else {
        $show_month = $month;
        $show_year = $year;
    }

    $dayindex = array();
    global $dayindex;
    $posts = $bBlog->_ph->get_posts(array("where" => " AND month(FROM_UNIXTIME(posttime)) = $show_month and year(FROM_UNIXTIME(posttime)) = $show_year ","num"=>"999"));
    if($posts){
        //var_dump($posts);
        if(!isset($posts['title'])){
            while ($post = $posts->FetchRow()) {
                $d = date('j', $post['posttime']);
                $dayindex[$d][] = array(
                    "id"    => $post['post'],
                    "title" => $post['title'],
                    "url"   => $bBlog->_get_entry_permalink($post['postid'])
                );
            }
        }
    }

    $right_year = $show_year;
    $left_year = $right_year;
    
    
    $left_month = $show_month - 1;
    if ($left_month < 1) {
    	$left_month = 12;
    	$left_year--;
    }
    $right_month = $show_month + 1;
    if ($right_month > 12) {
    	$right_month = 1;
    	$right_year++;
    }
    
    $bBlog->assign("left", $_SERVER["PHP_SELF"] . "?month=$left_month&year=$left_year");
    $bBlog->assign("right", $_SERVER["PHP_SELF"] . "?month=$right_month&year=$right_year");
    
    $bBlog->assign("header", strftime("%B %Y", mktime(0, 0, 0, $show_month, 1, $show_year)));

    $first_date = mktime(0, 0, 0, $show_month, 1, $show_year);
    $date = getdate($first_date);
    $first_wday = $date["wday"];
    $last_date = mktime(0, 0, 0, $show_month + 1, 0, $show_year);
    $date = getdate($last_date);
    $last_day = $date["mday"];
    
    $wday = array();
    if ($params["locale"])
	   @setlocale(LC_TIME, $params["locale"]);
    $week_start = (isset($params["week_start"])) ? $params['week_start'] : 0;
    if ($week_start < 0 || $week_start > 6) {
        $week_start = 1;
    }
    //To generate the date, we use March, 2004 as the first day of the month falls on a sunday
    //This way we are assured of generating the weekday names in order without needing AI 
    for ($counter = $week_start; $counter < $week_start + 7; $counter++) {
        $wday[] = strftime('%a', mktime(0,0,0,3,$counter,2004));
    }
	
    $bBlog->assign("wday", $wday);
    
    $week_array = array();
    $month_array = array();

    $pre_counter = $first_wday - $week_start;
    if ($pre_counter < 0)
	   $pre_counter += 7;
    
    $day = 1;
    while($day < $last_day) {
	   $week_array = array();
    	for ($counter = 0; $counter < 7; $counter++) {
    	    if ($day > $last_day) {
    		$week_array[] = array(
    			0 => false,
    			1 => "&nbsp;",
    			2 => false
    		    );
    	    }
            else if ($pre_counter > 0) {
                $week_array[] = array(
                    0 => false,
        			1 => "&nbsp;",
        			2 => false
    		    );
    		$pre_counter--;
    	    }
            else {
                getDateLink($day, $values);
                $week_array[] = array(
        			0 => (($dayindex["$day"])?true:false),
        			1 => $day,
        			2 => (($day == $today && $month == $show_month && $year == $show_year)?true:false)
    		    );
                $day++;
    	    }
    	}
    	$month_array[] = $week_array;
    }
    
    $bBlog->assign("month", $month_array);
    $bBlog->assign("values", $values);

    $bBlog->display("calendar.html",FALSE);

}

function getDateLink($day, $values) {

    global $dayindex;

    if (!$dayindex[$day]) {
        return;
    }
    else {
        $script = '';
    	foreach($dayindex[$day] as $item) {
    	    $script .= sprintf("&raquo; <a href=\"%s\">%s</a><br>", $item['url'],$item['title']);
    	}
    	$values .= 'cc[$day]="'.$script.'";';
    }
}

?>