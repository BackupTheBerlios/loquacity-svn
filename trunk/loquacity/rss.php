<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Syndication
 * @author Eaden McKee, http://www.bblog.com
 * @copyright &copy; 2005 Eaden McKee <email@eadz.co.nz>
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
 * RSS Feed Generator
 *
 * Returns an RSS 2.0/1.0 or an Atom03 feed when requested by the user 
 *
 * @version $Revision$
 */
if(!defined('CUSTOMRSS')) {
// so for example you could use this file but include it instead of calling it directly..
	include "core/config.php";
    $ver = @$_GET['ver'];
	$num = @$_GET['num'];
	$sectionid = @$_GET['sectionid'];
	$section = @$_GET['section'];
	$year = @$_GET['year'];
	$month = @$_GET['month'];
	$day = @$_GET['day'];
}
$p = array();
if(is_numeric($num)) $p['num'] = $num;
	else $p['num'] = 10;

if(is_numeric($sectionid)) $p['sectionid'] = $sectionid;

if(strlen($sectionname) >0) {
	$sid = $loq->sect_by_name[$sectionname];
	if(is_numeric($sid) && $sid>0)
		$p['sectionid'] = $sid;
}

if(is_numeric($year)) $p['year'] = $year;
if(is_numeric($year)) $p['month'] = $month;
if(is_numeric($year)) $p['day'] = $day;

$posts = $loq->get_posts($loq->make_post_query($p));
$loq->assign('posts',$posts);

$loq->template_dir = LOQ_APP_ROOT.'inc/admin_templates';
$loq->compile_id = 'admin';

// Format last modification date for use in the header.
$last_modified = gmdate ("D, d M Y H:i:s \G\M\T", C_LAST_MODIFIED);
$last_modified_hash = (isset($last_modified) ? md5($last_modified) : '');

// Set the Last-Modified and Etag headers.
header("Last-Modified: {$last_modified}",true);
header("Etag: {$last_modified_hash}",true);

switch ($ver){
    case '2.0':
        header("Content-Type: application/rss+xml",true);
        $loq->display('rss20.html',false);
        break;
	case '1.0':
		header("Content-Type: application/rss+xml",true);
		$loq->display('rss10.html',false);
		break;
		
    case 'atom03':
        header('Content-type: application/atom+xml', true);
        $loq->display('atom.html',false);
        break;

    default:
        header("Content-Type: text/xml",true);
        $loq->display('rss092.html',false);
        break;    

}
?>
