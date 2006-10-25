<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Chris Boulton <c.boulton@mybboard.com> 
 * @copyright &copy; 2004 Chris Boulton <c.boulton@mybboard.com> 
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
 * Build Archive Months Listing 
 */

function identify_function_archivemonths(){
    return array(
        "name" => "archivemonths",
        "type" => "function",
        "nicename" => "Archive Month Listing",
        "description" => "Generates a list of archive months",
        "authors" => "c.boulton@mybboard.com, Kenneth Power <telcor@users.berlios.de>",
        'license' => 'GPL'
    );
}

/**
 * Produces a list of months containing posts for easy access.
 * 
 * The list produced is of the following format:
 *     month year (posts)
 * 
 * Where month is the full string representing the month in the blog primary language,
 * year is the full four digit gregorian year and posts is the number of posts total for
 * that month. Each entry is on its own line and the months begin with current - 1
 * 
 * This is an HTML GUI function, returning data in HTML markup for placement on your
 * template.
 * 
 * @param array $params A list of parameters that define the behavior and look of the
 *              resulting list. Currently the array recognizes the following:
 *              sep        => When not in HTML list mode, specifies the HTML tag used to separate list items. If nothing is specified, and we are not in HTML list mode, this defaults to <br />
 *              showcount  => 0 or 1 (false or true) When set to false, do not display (posts) as part of the list entry
 *              year       => If four digit year is specified, only show months for the given year
 *              num        => Show at most `num` list items
 *              aslist     => Switches formating to HTML list mode. This produces a list in unordered list markup (<ul>)
 */
function smarty_function_archivemonths($params, &$loq) {
    $sep = (isset($params['sep'])) ? $params['sep'] : "<br />";
    $list = (isset($params['aslist'])) ? true : false;
    $showcount = (isset($params['showcount']) && intval($params['showcount']) == 0) ? false : true;
    $year = (isset($params['year'])) ? 'YEAR(FROM_UNIXTIME(posttime)) = "'.intval($params['year']).'"' : '';
    if($year != '')
        $where = 'WHERE '.$year;
    $num = (isset($params['num'])) ? intval($params['num']) : 10;
    $limit = 'limit 0, ' . $num;
    
    $sql = 'SELECT DISTINCT YEAR(FROM_UNIXTIME(posttime)) AS `year`, DATE_FORMAT(FROM_UNIXTIME(posttime), "%M") AS `month`, COUNT(postid) AS `posts` FROM `'.T_POSTS.'` '.$where.' GROUP BY `year`, `month` ORDER BY posttime DESC '.$limit;
    $rs = $loq->_adb->Execute($sql);
    if($rs && !$rs->EOF){
        if($list)
            $monthslist = '<ul>';
        while($row = $rs->FetchRow()){
            if($list)
                $monthslist .= '<li>';
            $monthslist .= '<a href="archives.php?month='.$row['month'].'&year='.$row['year'].'">'.$row['month']. '&nbsp;'. $row['year'].'</a>';
            if($showcount) {
                $monthslist .= ' <em>('.$row['posts'].')</em>';
            }
            if($list)
                $monthslist .= '</li>';
            else
                $monthslist .= $sep;
        }
        if($list)
            $monthslist .='</ul>';
    }
    return $monthslist;
    //var_dump($sql);
}

function getmonthfriendlyname($month) {
    $tstamp = mktime(0, 0, 0, $month);
    return strftime('%m', $tstamp);
}
?>
