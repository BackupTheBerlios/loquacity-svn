<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Plugins
 * @author Martin Konicek
 * @copyright &copy; 2003 Martin Konicek <martin.konicek@atlas.cz>
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
  * administer rss
  */
  
function identify_admin_rss () {
$help = '
<p>
<i>function </i><b>Get RSS</b><br>
</p>
<p><b><i>example: </b></i>{getrss} - select random RSS Feed<br>
<p><b><i>example: </b></i>{getrss id=1} - select defined RSS Feed
</p>';

return array (
    'name'           =>'rss',
    'type'             =>'admin',
    'nicename'     =>'RSS Fetcher',
    'description'   =>'Edit RSS Feeds',
    'authors'        =>'Martin Konicek <martin.konicek@atlas.cz>',
    'licence'         =>'GPL',
    'template' 	=> 'rss.html',
    'help'    	=> $help
  );
}


function admin_plugin_rss_run(&$loq) {

	$pole = "";
	for($i=1; $i<10; $i++) 
	{
		if ((isset($_POST['sending'])) && ($_POST['sending']=="true"))
		{
			$id = $_POST[id.$i];
			$ch = $_POST[ch.$i];
			$update_query = "UPDATE ".T_RSS." SET `url` = '".$id."',`input_charset` = '".$ch."' WHERE `id` = '".$i."' LIMIT 1 ;";
			$loq->_adb->Execute($update_query);
		}

        $row = array();
		$query = "select * from ".T_RSS." where id=".$i.";";
        $rs = $loq->_adb->Execute($query);
        if($rs !== false && !$rs->EOF){
            $row = $rs->FetchRow();
        }
		$rssurl = $row['url'];
		$w1250 = "";
		if ($row['input_charset']=="W1250") {$w1250=" selected";}
		$utf8 = "";		
		if ($row['input_charset']=="UTF8") {$utf8=" selected";}

		if ($i / 2 == floor($i /2)) $class = 'high';
		else $class = 'low';

		$pole.='<tr class="'.$class.'"><td>'.$i.'</td><td><input type="text" name="id'.$i.'" size="20" value="'.$rssurl.'" class="text" /></td><td><select name="ch'.$i.'">';
		$pole.='<option>I88592</option>';
		$pole.='<option'.$w1250.'>W1250</option>';
		$pole.='<option'.$utf8.'>UTF8</option>';
		$pole.='</select></td></tr>';
	}

	$loq->assign('pole',$pole);
}
?>
