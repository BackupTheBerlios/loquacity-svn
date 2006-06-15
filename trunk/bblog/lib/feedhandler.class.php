<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (C) 2006 Kenneth Power <telcor@users.berlios.de>
 *
 * @package Loquacity
 * @subpackage Syndication
 * @author Kenneth Power <telcor@users.berlios.de>
 * @copyright &copy; 2006 Kenneth Power
 * @license http://www.opensource.org/licenses/lgpl-license.php GPL
 * @link http://www.loquacity.info
 * @since 0.8-alpha2
 *
 * LICENSE:
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
* A wrapper class for the FeedCreator library
*
* The sole purpose of the wrapper class is to integrate the library into the project
*
* @version $Revision$
*/
class feedhandler{
    function feedhandler(&$db, $ph){
        $this->_db =& $db;
        $this->_ph =& $ph;
        $this->_types = array('RSS0.92', 'RSS1.0', 'RSS2.0', 'ATOM1.0', 'ATOM03', 'MBOX', 'OPML', 'HTML', 'JS');
    }
    /**
    * Generate the feed fully populated. Defaults to RSS2.0
    *
    *
    *  @param string $f Optional. Name of file to save feed to
    *  @param string $type Optional. Which type of feed to generate. Supported are RSS2.0, RSS1.0, RSS0.92, ATOM1.0, ATOM03
    *  @return mixed If a filename is passed, then nothing is returned, otherwise an XML datastream
    */
    function generate($type=null,$f=null){
        include_once('ext-libs/feedcreator/feedcreator.class.php');
        $ft = (!is_null($type) && count($ft) > 0) ? $type : 'RSS2.0';
        $feed = new UniversalFeedCreator();
        $feed->UseCached();
        $feed->title = C_BLOGNAME;
        $feed->description = C_BLOG_DESCRIPTION;
        $feed->link = BLOGURL;
        $feed->syndicationURL = BLOGURL.basename($_SERVER['SCRIPT_NAME']);

        $posts = $this->_ph->get_posts(array('order' => 'ORDER BY posttime DESC', 'num' => 20));
        foreach($posts as $post){
            $item = new FeedItem();
            $item->title = $post['title'];
            $item->link = $post['permalink'];
            $item->description = $post['body'];
            $item->source = $rss->link;
            $item->author = $post['author']['fullname'];
            $feed->additem($item);
        }
        if(!is_null($f)){
            if(file_exists($f) && is_writable($f)){
                $feed->savefeed($ft, $f);
            }//right here we need to generate and catch an error
        }
        else{
            $feed->outputfeed($ft);
        }
    }
    function createurl(){
        $url = BLOGURL.'feed.php?';
        $type = (isset($_POST['f_type'])) ? strtoupper($_POST['f_type']) : 'RSS2.0';
        if(!in_array($type, $this->_types)){
            $type = 'RSS2.0';
        }
        $posts = (isset($_POST['f_posts'])) ? intval($_POST['f_posts']) : 20;
        if($posts === 0){
            $posts = 20;
        }
        $section = (isset($_POST['f_section'])) ? intval($_POST['f_section']) : null;
        if(!is_null($section) && $section === 0){
            $section = null;
        }

        $url .='ft='.urlencode($type);
        $url .='qty='.urlencode($posts);
        if(!is_null($section)){
            $url .= 's='.urlencode($sectopm);
        }
        return $url;
    }
}
?>