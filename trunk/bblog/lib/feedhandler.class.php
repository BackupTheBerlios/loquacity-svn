<?oho
/* Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (C) 2006 Kenneth Power <telcor@users.berlios.de>
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

class feedhandler{
    function feedhandler(&$db){
        $this->_db =& $db;
        $this->_types = array('RSS0.92', 'RSS1.0', 'RSS2.0', 'ATOM1.0', 'ATOM03', 'MBOX', 'OPML', 'HTML', 'JS');
    }
    function generate($f=null){
        include_once('libs/feedcreator/feedcreator.class.php');
        $feed = new UniversalFeedCreator();
        $feed->UseCached();
        $feed->title = C_BLOGNAME;
        $feed->description = C_BLOG_DESCRIPTION;
        $feed->link = BLOGURL;
        $feed->syndicationURL = BLOGURL.basename($_SERVER['SCRIPT_NAME']);
        
        $ph = $bBlog->_ph;
        $posts = $ph->get_posts(array('order' => 'ORDER BY posttime DESC', 'num' => 20));
        foreach($posts as $post){
            $item = new FeedItem();
            $item->title = $post['title'];
            $item->link = $post['permalink'];
            $item->description = $post['body'];
            $item->source = $rss->link;
            $item->author = $post['author']['fullname'];
            $feed->additem($item);
        }
        $feed->outputfeed('RSS2.0');
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
