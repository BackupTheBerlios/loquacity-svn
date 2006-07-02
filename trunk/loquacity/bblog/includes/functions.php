<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Administration
 * @author Eaden McKee <email@eadz.co.nz>
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
 * General functions for bBlog that don't fit elsewhere
 *
 * @version $Revision$
 */                              
////
// !Pings weblogs.com, blo.gs, and others in the future
// this is in it's own function so we can use register_shutdown_function
function ping() {

    $weblogsparams[0] = C_BLOGNAME;
    $weblogsparams[1] = BLOGURL;
    $sites = explode(',',C_PING);
    if(count($sites) > 0) {
	foreach($sites as $site) {
		$url = explode('/',$site);
		XMLRPC_request($url[0], "/".$url[1], "weblogUpdates.ping", $weblogsparams, WEBLOG_XMLRPC_USERAGENT );
	}
    }

}


////
// !runs my_addslashes on an array item
// used by my_addslashes_array_walk
function my_addslashes_array(&$item,$key) {
       $item = my_addslashes($item);
} // end of my_addslashes_array()

////
// !runs my_addslashes over an array
// $array is not passed by reference so it makes a copy.
function my_addslashes_array_walk($array) {
       array_walk($array,'my_addslashes_array');
       return $array;
}


function update_when_compiled($tpl_source, &$bBlog) {
    if(!defined('IN_BBLOG_ADMIN')) {
      //$bBlog->modified_now();
    }
    return $tpl_source;
}
?>
