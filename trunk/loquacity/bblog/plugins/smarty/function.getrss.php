<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Martin Konicek <martin.konicek@atlas.cz>
 * @copyright &copy; 2003  Martin Konicek <martin.konicek@atlas.cz>
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
 
// Modified inc/init.php
//	Libraries

$library_dir = dirname(__FILE__).'/rss/';
require_once($library_dir.'rss.php');

function identify_function_getrss() {
$help = '
<p>
<i>function </i><b>Get Recent Posts</b><br>
</p>
<p><b><i>example: </b></i>{getrss} - select random RSS Feed<br>
<p><b><i>example: </b></i>{getrss id=1} - select defined RSS Feed
<p><b><i>example: </b></i>{getrss id=1 limit=10} - Only show 10 items
</p>';

  return array (
    'name'           =>'getrss',
    'type'             =>'function',
    'nicename'     =>'Get RSS 0.1.2 alpha',
    'description'   =>'Parse RSS to HTML',
    'authors'        =>'Martin Konicek <martin.konicek@atlas.cz>',
    'licence'         =>'GPL',
    'help'   => $help
  );
}

function smarty_function_getrss($params, &$bBlog) { 
	$outputcharset='UTF8';
	if(isset($params['id'])) {
        $rs = $bBlog->_adb->Execute("select * from ".T_RSS." where url<>'' and id='".$params['id']."'");
        if($rs !== false && !$rs->EOF){
            $rssrow = $rs->FetchRow();
        }
	}
    else { // get random one
        $rs = $bBlog->_adb->Execute("select * from ".T_RSS." where url<>'' order by rand(".time().") limit 0,1");
        if($rs !== false && !$rs->EOF){
            $rssrow = $rs->FetchRow();
        }
	}

	if (!isset ($params['limit']))
		$params['limit'] = 20;

	return get_rss($rssrow['url'],$rssrow['input_charset'],$outputcharset,$params['limit']);

}

?>