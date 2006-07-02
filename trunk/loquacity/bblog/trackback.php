<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Comments
 * @author Eaden McKee <email@eadz.co.nz>
 * @copyright Copyright &copy; 2003  Eaden McKee <email@eadz.co.nz>
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
 * Trackback handling
 *
 * Receives a trackback, and functions for sending a trackback
 *
 * @version $Revision$
 */

if(!defined('IN_LOQUACITY')){
    include_once('./config.php');
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded'){
    $post = null;
    $comment = null;
    if(defined('CLEANURLS')){
        $url = explode('/', $_SERVER['REQUEST_URI']);
        $num = count($url);
        if($url[$num - 3] === 'trackback'){ //a comment id is included
            $post = stringHandler::removeMagicQuotes($url[$num - 2]);
            $comment = stringHandler::removeMagicQuotes($url[$num - 1]);
        }
        else{
            $post = stringHandler::removeMagicQuotes($url[$num - 1]);
        }
    }
    else{
        $url = array();
        parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $url);
        $post = stringHandler::removeMagicQuotes($url['tbpost']);
        if(isset($url['cid'])){
            $comment = stringHandler::removeMagicQuotes($url['cid']);
            }
    }
    include_once('lib/trackbackhandler.class.php');
    $th = new trackbackhandler($bBlog->_adb, $post);
    $th->receiveTrackback($_SERVER['REMOTE_ADDR'], $_POST, $comment);
}
?>