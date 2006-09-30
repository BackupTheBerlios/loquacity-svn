<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Eaden McKee, http://www.bblog.com
 * @copyright &copy; 2003  Eaden McKee <email@eadz.co.nz>
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
 * Displays information about bBlog/Loquacity
 */
 
function identify_admin_help () {
  return array (
    'name'           =>'about',
    'type'           =>'builtin',
    'nicename'       =>'About',
    'description'    =>'Displays Loquacity information',
    'authors'         =>'Eaden McKee <email@eadz.co.nz>',
    'licence'         =>'GPL'
  );
}
include_once('includes/credits.php');
$bBlog->assign('credits',$credits);
$bBlog->assign('title','About Loquacity '.BBLOG_VERSION);

ob_start();
include_once('docs/LICENCE.txt');
$bBlog->assign('licence',ob_get_contents());
ob_end_clean();

ob_start();
include_once('make_bookmarklet.php');
$bBlog->assign('bookmarklet',ob_get_contents());
ob_end_clean();

$bBlog->display("about.html");
?>
