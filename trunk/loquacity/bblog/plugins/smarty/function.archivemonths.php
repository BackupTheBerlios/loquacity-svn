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

function identify_function_archivemonths() {
return array("name" => "archivemonths",
"type" => "function",
"nicename" => "Archive Month Listing",
"description" => "Generates a list of archive months",
"authors" => "c.boulton@mybboard.com");
}
function smarty_function_archivemonths($params, &$bBlog) {
$num = 10;
$sep = "<br />";
$year = "";
$showcount = 0;
extract($params);
if($year) {
$where = " AND YEAR(FROM_UNIXTIME(posttime)) = '$year'";
}
if($num) {
$num = " LIMIT 0, $num";
}
$query = mysql_query("SELECT DISTINCT YEAR(FROM_UNIXTIME(posttime)) AS year, MONTH(FROM_UNIXTIME(posttime)) AS month, COUNT(postid) AS posts FROM ".T_POSTS." $where GROUP BY YEAR(FROM_UNIXTIME(posttime)), MONTH(FROM_UNIXTIME(posttime)) ORDER BY posttime DESC $limit;");
while($month = mysql_fetch_array($query)) {
if($month[month] < 10) {
$month[month] = "0$month[month]";
}
$monthslist .= "<a href=\"archives.php?month=$month[month]&year=$month[year]\">".getmonthfriendlyname($month[month])." $month[year]</a> ";
if($showcount) {
$monthslist .= " <i>$month[posts]</i>";
}
$monthslist .= "$sep";
}
return $monthslist;
}
function getmonthfriendlyname($month) {
$tstamp = mktime(0, 0, 0, $month);
return date("F", $tstamp);
}
?>