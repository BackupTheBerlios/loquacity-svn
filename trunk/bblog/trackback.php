<?php
// trackback.php - Recieves a trackback, and functions for sending a trackback
// trackback.php - author: Eaden McKee <email@eadz.co.nz>
/*
** bBlog Weblog http://www.bblog.com/
** Copyright (C) 2003  Eaden McKee <email@eadz.co.nz>
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
            $post = StringHandling::removeMagicQuotes($url[$num - 2]);
            $comment = StringHandling::removeMagicQuotes($url[$num - 1]);
        }
        else{
            $post = StringHandling::removeMagicQuotes($url[$num - 1]);
        }
    }
    else{
        $url = array();
        parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $url);
        $post = StringHandling::removeMagicQuotes($url['tbpost']);
        if(isset($url['cid'])){
            $comment = StringHandling::removeMagicQuotes($url['cid']);
            }
    }
    include_once('lib/trackbackhandler.class.php');
    $th = new trackbackhandler(&$bBlog->_adb, $post);
    $th->receiveTrackback($_SERVER['REMOTE_ADDR'], $_POST, $comment);
}
?>