<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Publishing
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
 * Multifunctional index file
 *
 * Shows the front page, a certain post or a section depending on the GET request
 *
 * @version $Revision$
 */
include_once("bblog/config.php");

if(isset($_GET['postid']) && is_numeric($_GET['postid'])) {
    if(isset($_COOKIE['bBcomment'])){
        $cdata = unserialize(base64_decode($_COOKIE['bBcomment']));
        $bBlog->assign('cdata',$cdata);
    }
    $bBlog->assign('postid',(int)$_GET['postid']);
    $bBlog->show_post = (int)$_GET['postid'];
    $bBlog->display('post.html');
}
else if(isset($_GET['sectionid']) && is_numeric($_GET['sectionid'])) {
   	$bBlog->assign('sectionid', (int)$_GET['sectionid']);
   	$bBlog->assign('sectionname',$bBlog->sect_by_name[(int)$_GET['sectionid']]);
   	$bBlog->show_section = (int)$_GET['sectionid'];
	$bBlog->display('index.html');
}
else {
	$bBlog->display('index.html');
}
?>
